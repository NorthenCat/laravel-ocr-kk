<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\RW;
use App\Models\Desa;
use App\Exports\AnggotaExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class RWController extends Controller
{
    private function getApiUrl()
    {
        $apiUrl = config('app.api_url');
        if (!$apiUrl) {
            $apiUrl = config('app.url', 'http://127.0.0.1:8000') . '/api';
        }
        return $apiUrl;
    }

    private function getApiToken()
    {
        return session('api_token');
    }

    private function getUserId()
    {
        $apiUser = session('api_user');
        return $apiUser['id'] ?? null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index($desa_id, $rw_id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();
            $userId = $this->getUserId();

            if (!$apiToken || !$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Get RW data from API
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->get($apiUrl . '/desa/' . $desa_id . '/rw/' . $rw_id, [
                    'user_id' => $userId
                ]);



            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    $data = $apiData['data'];
                    // Sync RW data to local database (for job status relationships)
                    $rwData = $data['rw'];
                    // Use DB::table for raw insert/update to handle specific IDs
                    $localRw = RW::where('uuid', $rwData['uuid'])->first();
                    if ($localRw) {
                        // Update existing record
                        RW::where('uuid', $rwData['uuid'])->update([
                            'nama_rw' => $rwData['nama_rw'],
                            'google_drive' => $rwData['google_drive'] ?? null,
                            'updated_at' => now(),
                        ]);
                    } else {
                        // Insert new record with specific ID
                        try {
                            RW::create([
                                'uuid' => $rwData['uuid'],
                                'nama_rw' => $rwData['nama_rw'],
                                'desa_id' => $rwData['desa_id'],
                                'google_drive' => $rwData['google_drive'] ?? null,
                                'created_at' => $rwData['created_at'],
                                'updated_at' => $rwData['updated_at'],
                            ]);
                        } catch (\Exception $e) {
                            dd('RW insert error:', $e->getMessage());
                        }
                    }

                    // Get the RW instance for relationships
                    $localRwInstance = RW::where('uuid', $rwData['uuid'])->first();

                    // Convert arrays to objects for compatibility with Blade views
                    $rw = (object) $data['rw'];
                    $rw->getDesa = (object) $rw->get_desa;
                    $rw->getKK = collect($rw->get_k_k ?? [])->map(function ($kk) {
                        $kkObj = (object) $kk;
                        $kkObj->getWarga = collect($kk['get_warga'] ?? [])->map(function ($warga) {
                            return (object) $warga;
                        });
                        return $kkObj;
                    });


                    // Get job status from local database using the synced RW
                    $jobStatus = $localRwInstance->getJobStatus()
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                    $rw->getJobStatus = $jobStatus;


                    // Get current job status from local
                    $currentJobStatus = $localRwInstance->getCurrentJobStatus;
                    $rw->getCurrentJobStatus = $currentJobStatus;

                    // Get local failed files since they're also stored locally
                    $localFailedFiles = \App\Models\FailedKkFile::where('rw_id', $rw_id)
                        ->where('manually_processed', false)
                        ->orderBy('created_at', 'desc')
                        ->get();


                    $standaloneAnggota = collect($data['standalone_anggota'] ?? [])->map(function ($anggota) {
                        $anggotaObj = (object) $anggota;
                        $anggotaObj->getKk = isset($anggota['get_kk']) ? (object) $anggota['get_kk'] : null;
                        return $anggotaObj;
                    });

                    $anggotaTanpaNik = collect($data['anggota_tanpa_nik'] ?? [])->map(function ($anggota) {
                        $anggotaObj = (object) $anggota;
                        $anggotaObj->getKk = isset($anggota['get_kk']) ? (object) $anggota['get_kk'] : null;
                        return $anggotaObj;
                    });

                    // Use local failed files since they're processed locally
                    $failedFiles = $localFailedFiles;

                    $totalRT = $data['total_rt'] ?? 0;

                    return view('rw.index', compact('rw', 'standaloneAnggota', 'anggotaTanpaNik', 'failedFiles', 'totalRT'));
                }
            }

            // Fallback to local data if API fails
            $rw = RW::with([
                'getDesa',
                'getKK.getWarga',
                'getKK' => function ($query) {
                    $query->orderBy('no_kk', 'asc');
                },
                'getCurrentJobStatus',
                'getJobStatus' => function ($query) {
                    $query->orderBy('created_at', 'desc')->limit(5);
                }
            ])->where('desa_id', $desa_id)->findOrFail($rw_id);

            $rw->loadCount(['getKK', 'getWarga']);

            // Get standalone anggota (those with zero KK only)
            $standaloneAnggota = Anggota::with('getKk')
                ->whereHas('getKk', function ($query) use ($rw_id) {
                    $query->where('rw_id', $rw_id)
                        ->where('no_kk', '0000000000000000');
                })
                ->orderBy('nama_lengkap', 'asc')
                ->get();

            // Get anggota without NIK
            $anggotaTanpaNik = Anggota::with('getKk')
                ->whereHas('getKk', function ($query) use ($rw_id) {
                    $query->where('rw_id', $rw_id);
                })
                ->where(function ($query) {
                    $query->whereNull('nik')
                        ->orWhere('nik', '')
                        ->orWhere('nik', '-');
                })
                ->orderBy('nama_lengkap', 'asc')
                ->get();

            // Get failed KK files for manual processing
            $failedFiles = \App\Models\FailedKkFile::where('rw_id', $rw_id)
                ->where('manually_processed', false)
                ->orderBy('created_at', 'desc')
                ->get();

            $totalRT = Anggota::whereHas('getKk', function ($query) use ($rw_id) {
                $query->where('rw_id', $rw_id);
            })
                ->pluck('rt')
                ->map(function ($rt) {
                    return (int) $rt;
                })
                ->unique()
                ->count();

            return view('rw.index', compact('rw', 'standaloneAnggota', 'anggotaTanpaNik', 'failedFiles', 'totalRT'));

        } catch (\Exception $e) {
            \Log::error('Failed to fetch RW data: ' . $e->getMessage());
            return redirect()->route('desa.show', $desa_id)->with('error', 'Failed to load RW data.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($desa_id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();
            $userId = $this->getUserId();

            if (!$apiToken || !$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Get desa data from API to check access
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->get($apiUrl . '/desa/' . $desa_id, [
                    'user_id' => $userId
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    $desa = (object) $apiData['data'];
                    return view('rw.form', compact('desa'));
                }
            }

            // Fallback to local data
            $desa = Desa::findOrFail($desa_id);
            return view('rw.form', compact('desa'));

        } catch (\Exception $e) {
            \Log::error('Failed to load desa for RW creation: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Failed to load desa data.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $desa_id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();
            $userId = $this->getUserId();

            if (!$apiToken || !$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Create RW via API
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->post($apiUrl . '/desa/' . $desa_id . '/rw', [
                    'nama_rw' => $request->nama_rw,
                    'google_drive' => $request->google_drive,
                    'user_id' => $userId,
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    $rwData = $apiData['data'];

                    // Sync the created RW to local database
                    RW::create([
                        'uuid' => $rwData['uuid'],
                        'nama_rw' => $rwData['nama_rw'],
                        'desa_id' => $rwData['desa_id'],
                        'google_drive' => $rwData['google_drive'] ?? null,
                        'created_at' => $rwData['created_at'],
                        'updated_at' => $rwData['updated_at'],
                    ]);

                    return redirect()->route('rw.index', [$desa_id, $rwData['id']])
                        ->with('success', 'RW created successfully.');
                }
            }

            // Handle API errors
            $errorData = $response->json();
            if (isset($errorData['errors'])) {
                return redirect()->back()
                    ->withErrors($errorData['errors'])
                    ->withInput();
            }

            return redirect()->back()
                ->withErrors(['error' => $errorData['message'] ?? 'Failed to create RW.'])
                ->withInput();

        } catch (\Exception $e) {
            \Log::error('Failed to create RW via API: ' . $e->getMessage());

            // Fallback to local creation
            $request->validate([
                'nama_rw' => 'required|string|max:255',
                'google_drive' => 'nullable|url|max:500',
            ]);

            $desa = Desa::findOrFail($desa_id);

            $rw = RW::create([
                'uuid' => Str::uuid(),
                'nama_rw' => $request->nama_rw,
                'google_drive' => $request->google_drive,
                'desa_id' => $desa->id,
            ]);

            return redirect()->route('rw.index', [$desa->id, $rw->id])->with('success', 'RW created successfully.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($desa_id, $rw_id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();
            $userId = $this->getUserId();

            if (!$apiToken || !$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Get RW data from API
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->get($apiUrl . '/desa/' . $desa_id . '/rw/' . $rw_id, [
                    'user_id' => $userId
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    $rw = (object) $apiData['data']['rw'];
                    $rw->getDesa = (object) $rw->get_desa;
                    return view('rw.form', compact('rw'));
                }
            }

            // Fallback to local data
            $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);
            return view('rw.form', compact('rw'));

        } catch (\Exception $e) {
            \Log::error('Failed to fetch RW for edit: ' . $e->getMessage());
            return redirect()->route('desa.show', $desa_id)->with('error', 'Failed to load RW data.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $desa_id, $rw_id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();
            $userId = $this->getUserId();

            if (!$apiToken || !$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Update RW via API
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->put($apiUrl . '/desa/' . $desa_id . '/rw/' . $rw_id, [
                    'nama_rw' => $request->nama_rw,
                    'google_drive' => $request->google_drive,
                    'user_id' => $userId,
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    $rwData = $apiData['data'];

                    // Sync the updated RW to local database
                    \DB::table('rw')->where('id', $rwData['id'])->update([
                        'uuid' => $rwData['uuid'],
                        'nama_rw' => $rwData['nama_rw'],
                        'desa_id' => $rwData['desa_id'],
                        'google_drive' => $rwData['google_drive'] ?? null,
                        'updated_at' => $rwData['updated_at'],
                    ]);

                    return redirect()->route('rw.index', [$desa_id, $rw_id])
                        ->with('success', 'RW updated successfully.');
                }
            }

            // Handle API errors
            $errorData = $response->json();
            if (isset($errorData['errors'])) {
                return redirect()->back()
                    ->withErrors($errorData['errors'])
                    ->withInput();
            }

            return redirect()->back()
                ->withErrors(['error' => $errorData['message'] ?? 'Failed to update RW.'])
                ->withInput();

        } catch (\Exception $e) {
            \Log::error('Failed to update RW via API: ' . $e->getMessage());

            // Fallback to local update
            $request->validate([
                'nama_rw' => 'required|string|max:255',
                'google_drive' => 'nullable|url|max:500',
            ]);

            $rw = RW::where('desa_id', $desa_id)->findOrFail($rw_id);
            $rw->update([
                'nama_rw' => $request->nama_rw,
                'google_drive' => $request->google_drive,
            ]);

            return redirect()->route('rw.index', [$desa_id, $rw->id])->with('success', 'RW updated successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($desa_id, $rw_id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();
            $userId = $this->getUserId();

            if (!$apiToken || !$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Delete RW via API
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->delete($apiUrl . '/desa/' . $desa_id . '/rw/' . $rw_id, [
                    'user_id' => $userId
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    return redirect()->route('desa.show', $desa_id)->with('success', 'RW deleted successfully.');
                }
            }

            return redirect()->back()->with('error', 'Failed to delete RW.');

        } catch (\Exception $e) {
            \Log::error('Failed to delete RW via API: ' . $e->getMessage());

            // Fallback to local deletion
            $rw = RW::where('desa_id', $desa_id)->findOrFail($rw_id);
            $rw->delete();

            return redirect()->route('desa.show', $desa_id)->with('success', 'RW deleted successfully.');
        }
    }

    /**
     * Export to Excel.
     */
    public function exportExcel($desa_id, $rw_id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();
            $userId = $this->getUserId();

            if (!$apiToken || !$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Get export data from API
            $response = Http::timeout(60) // Increase timeout for file generation
                ->withToken($apiToken)
                ->get($apiUrl . '/desa/' . $desa_id . '/rw/' . $rw_id . '/export-excel', [
                    'user_id' => $userId
                ]);

            if ($response->successful()) {
                // Check if response is a file (binary data)
                $contentType = $response->header('Content-Type') ?? '';

                if (
                    str_contains($contentType, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') ||
                    str_contains($contentType, 'application/octet-stream')
                ) {

                    // Get filename from Content-Disposition header or create default
                    $contentDisposition = $response->header('Content-Disposition');
                    $fileName = 'export.xlsx';

                    if ($contentDisposition && preg_match('/filename="([^"]+)"/', $contentDisposition, $matches)) {
                        $fileName = $matches[1];
                    } else {
                        // Create default filename
                        $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);
                        $fileName = 'KK-OCR_' . $rw->getDesa->nama_desa . '_' . $rw->nama_rw . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
                    }

                    return response($response->body())
                        ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                        ->header('Cache-Control', 'max-age=0');
                }
            }

            // Fallback to local export if API doesn't return file
            $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);
            $fileName = 'KK-OCR_' . $rw->getDesa->nama_desa . '_' . $rw->nama_rw . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            return Excel::download(new AnggotaExport($rw_id, true), $fileName);

        } catch (\Exception $e) {
            \Log::error('Failed to export via API: ' . $e->getMessage());

            // Fallback to local export
            $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);
            $fileName = 'KK-OCR_' . $rw->getDesa->nama_desa . '_' . $rw->nama_rw . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            return Excel::download(new AnggotaExport($rw_id, true), $fileName);
        }
    }

    /**
     * Export to Excel without filename.
     */
    public function exportExcelWithoutFilename($desa_id, $rw_id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();
            $userId = $this->getUserId();

            if (!$apiToken || !$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Get export data from API
            $response = Http::timeout(60) // Increase timeout for file generation
                ->withToken($apiToken)
                ->get($apiUrl . '/desa/' . $desa_id . '/rw/' . $rw_id . '/export-excel-no-filename', [
                    'user_id' => $userId
                ]);

            if ($response->successful()) {
                // Check if response is a file (binary data)
                $contentType = $response->header('Content-Type') ?? '';

                if (
                    str_contains($contentType, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') ||
                    str_contains($contentType, 'application/octet-stream')
                ) {

                    // Get filename from Content-Disposition header or create default
                    $contentDisposition = $response->header('Content-Disposition');
                    $fileName = 'export.xlsx';

                    if ($contentDisposition && preg_match('/filename="([^"]+)"/', $contentDisposition, $matches)) {
                        $fileName = $matches[1];
                    } else {
                        // Create default filename
                        $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);
                        $fileName = 'KK-OCR_NoFilename_' . $rw->getDesa->nama_desa . '_' . $rw->nama_rw . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
                    }

                    return response($response->body())
                        ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                        ->header('Cache-Control', 'max-age=0');
                }
            }

            // Fallback to local export if API doesn't return file
            $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);
            $fileName = 'KK-OCR_NoFilename_' . $rw->getDesa->nama_desa . '_' . $rw->nama_rw . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            return Excel::download(new AnggotaExport($rw_id, false), $fileName);

        } catch (\Exception $e) {
            \Log::error('Failed to export via API: ' . $e->getMessage());

            // Fallback to local export
            $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);
            $fileName = 'KK-OCR_NoFilename_' . $rw->getDesa->nama_desa . '_' . $rw->nama_rw . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            return Excel::download(new AnggotaExport($rw_id, false), $fileName);
        }
    }
}
