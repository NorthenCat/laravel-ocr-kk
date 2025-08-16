<?php

namespace App\Http\Controllers;

use App\Models\KK;
use App\Models\RW;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class KKController extends Controller
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

    public function index($desa_id, $rw_id, $kk_id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();
            $userId = $this->getUserId();

            if (!$apiToken || !$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Get KK data from API
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->get($apiUrl . '/desa/' . $desa_id . '/rw/' . $rw_id . '/kk/' . $kk_id, [
                    'user_id' => $userId
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    $kkData = $apiData['data'];

                    // Convert to object for view compatibility
                    $kk = (object) $kkData;
                    $kk->getRw = (object) $kk->get_rw;
                    $kk->getRw->getDesa = (object) $kk->getRw->get_desa;
                    $kk->getWarga = collect($kk->get_warga ?? [])->map(function ($warga) {
                        return (object) $warga;
                    });
                    $kk->get_warga_count = count($kk->getWarga);

                    return view('kk.index', compact('kk'));
                }
            }

            // Fallback to local data if API fails
            $kk = KK::with([
                'getRw.getDesa',
                'getWarga' => function ($query) {
                    $query->orderBy('nama_lengkap', 'asc');
                }
            ])->where('rw_id', $rw_id)->findOrFail($kk_id);

            $kk->loadCount('getWarga');

            return view('kk.index', compact('kk'));

        } catch (\Exception $e) {
            \Log::error('Failed to fetch KK data: ' . $e->getMessage());
            return redirect()->route('rw.index', [$desa_id, $rw_id])->with('error', 'Failed to load KK data.');
        }
    }
    public function create($desa_id, $rw_id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();
            $userId = $this->getUserId();

            if (!$apiToken || !$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Get RW data from API to check access
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->get($apiUrl . '/desa/' . $desa_id . '/rw/' . $rw_id, [
                    'user_id' => $userId
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    $rwData = $apiData['data']['rw'];
                    $rw = (object) $rwData;
                    $rw->getDesa = (object) $rw->get_desa;
                    return view('kk.form', compact('rw'));
                }
            }

            // Fallback to local data
            $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);
            return view('kk.form', compact('rw'));

        } catch (\Exception $e) {
            \Log::error('Failed to load RW for KK creation: ' . $e->getMessage());
            return redirect()->route('rw.index', [$desa_id, $rw_id])->with('error', 'Failed to load RW data.');
        }
    }

    public function store(Request $request, $desa_id, $rw_id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();
            $userId = $this->getUserId();

            if (!$apiToken || !$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Create KK via API
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->post($apiUrl . '/desa/' . $desa_id . '/rw/' . $rw_id . '/kk', [
                    'no_kk' => $request->no_kk,
                    'nama_kepala_keluarga' => $request->nama_kepala_keluarga,
                    'user_id' => $userId,
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    return redirect()->route('kk.index', [$desa_id, $rw_id, $apiData['data']['id']])
                        ->with('success', 'KK created successfully.');
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
                ->withErrors(['error' => $errorData['message'] ?? 'Failed to create KK.'])
                ->withInput();

        } catch (\Exception $e) {
            \Log::error('Failed to create KK via API: ' . $e->getMessage());

            // Fallback to local creation
            $request->validate([
                'no_kk' => 'required|string|max:20',
                'nama_kepala_keluarga' => 'required|string|max:255',
            ]);

            $rw = RW::where('desa_id', $desa_id)->findOrFail($rw_id);

            // Check if KK number already exists in this RW
            $existingKK = KK::where('rw_id', $rw->id)->where('no_kk', $request->no_kk)->first();
            if ($existingKK) {
                return back()->withInput()->withErrors(['no_kk' => 'Nomor KK sudah terdaftar di RW ini.']);
            }

            $kk = KK::create([
                'uuid' => Str::uuid(),
                'no_kk' => $request->no_kk,
                'nama_kepala_keluarga' => $request->nama_kepala_keluarga,
                'rw_id' => $rw->id,
            ]);

            return redirect()->route('kk.index', [$desa_id, $rw_id, $kk->id])->with('success', 'KK created successfully.');
        }
    }


    public function edit($desa_id, $rw_id, $kk_id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();
            $userId = $this->getUserId();

            if (!$apiToken || !$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Get KK data from API
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->get($apiUrl . '/desa/' . $desa_id . '/rw/' . $rw_id . '/kk/' . $kk_id, [
                    'user_id' => $userId
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    $kkData = $apiData['data'];
                    $kk = (object) $kkData;
                    $kk->getRw = (object) $kk->get_rw;
                    $kk->getRw->getDesa = (object) $kk->getRw->get_desa;
                    return view('kk.form', compact('kk'));
                }
            }

            // Fallback to local data
            $kk = KK::with('getRw.getDesa')->where('rw_id', $rw_id)->findOrFail($kk_id);
            return view('kk.form', compact('kk'));

        } catch (\Exception $e) {
            \Log::error('Failed to fetch KK for edit: ' . $e->getMessage());
            return redirect()->route('rw.index', [$desa_id, $rw_id])->with('error', 'Failed to load KK data.');
        }
    }

    public function update(Request $request, $desa_id, $rw_id, $kk_id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();
            $userId = $this->getUserId();

            if (!$apiToken || !$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Update KK via API
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->put($apiUrl . '/desa/' . $desa_id . '/rw/' . $rw_id . '/kk/' . $kk_id, [
                    'no_kk' => $request->no_kk,
                    'nama_kepala_keluarga' => $request->nama_kepala_keluarga,
                    'user_id' => $userId,
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    return redirect()->route('kk.index', [$desa_id, $rw_id, $kk_id])
                        ->with('success', 'KK updated successfully.');
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
                ->withErrors(['error' => $errorData['message'] ?? 'Failed to update KK.'])
                ->withInput();

        } catch (\Exception $e) {
            \Log::error('Failed to update KK via API: ' . $e->getMessage());

            // Fallback to local update
            $request->validate([
                'no_kk' => 'required|string|max:20',
                'nama_kepala_keluarga' => 'required|string|max:255',
            ]);

            $kk = KK::where('rw_id', $rw_id)->findOrFail($kk_id);

            // Check if KK number already exists in this RW (excluding current KK)
            $existingKK = KK::where('rw_id', $kk->rw_id)
                ->where('no_kk', $request->no_kk)
                ->where('id', '!=', $kk->id)
                ->first();

            if ($existingKK) {
                return back()->withInput()->withErrors(['no_kk' => 'Nomor KK sudah terdaftar di RW ini.']);
            }

            $kk->update([
                'no_kk' => $request->no_kk,
                'nama_kepala_keluarga' => $request->nama_kepala_keluarga,
            ]);

            return redirect()->route('kk.index', [$desa_id, $rw_id, $kk->id])->with('success', 'KK updated successfully.');
        }
    }

    public function destroy($desa_id, $rw_id, $kk_id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();
            $userId = $this->getUserId();

            if (!$apiToken || !$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Delete KK via API
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->delete($apiUrl . '/desa/' . $desa_id . '/rw/' . $rw_id . '/kk/' . $kk_id, [
                    'user_id' => $userId
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    return redirect()->route('rw.index', [$desa_id, $rw_id])->with('success', 'KK deleted successfully.');
                }
            }

            return redirect()->back()->with('error', 'Failed to delete KK.');

        } catch (\Exception $e) {
            \Log::error('Failed to delete KK via API: ' . $e->getMessage());

            // Fallback to local deletion
            $kk = KK::where('rw_id', $rw_id)->findOrFail($kk_id);
            $kk->delete();

            return redirect()->route('rw.index', [$desa_id, $rw_id])->with('success', 'KK deleted successfully.');
        }
    }

    public function showUpload($desa_id, $rw_id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();
            $userId = $this->getUserId();

            if (!$apiToken || !$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Get RW data from API to check access
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->get($apiUrl . '/desa/' . $desa_id . '/rw/' . $rw_id, [
                    'user_id' => $userId
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    $rwData = $apiData['data']['rw'];
                    $rw = (object) $rwData;
                    $rw->getDesa = (object) $rw->get_desa;
                    return view('kk.upload', compact('rw'));
                }
            }

            // Fallback to local data
            $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);
            return view('kk.upload', compact('rw'));

        } catch (\Exception $e) {
            \Log::error('Failed to load RW for upload: ' . $e->getMessage());
            return redirect()->route('rw.index', [$desa_id, $rw_id])->with('error', 'Failed to load RW data.');
        }
    }

    public function processUpload(Request $request, $desa_id, $rw_uuid)
    {
        $request->validate([
            'kk_images' => 'required|array|min:1',
            'kk_images.*' => 'required|file|image|mimes:jpeg,jpg,png|max:10240', // 10MB max per image
        ]);

        try {
            // Check if webhook is configured
            $setting = \App\Models\Setting::first();
            if (!$setting || !$setting->webhook_n8n) {
                return back()->withErrors(['kk_images' => 'N8N webhook URL is not configured. Please contact administrator.']);
            }

            //Get RW from Local
            $rw_id = RW::where('uuid', $rw_uuid)->value('id');
            if (!$rw_id) {
                return back()->withErrors(['kk_images' => 'RW not found']);
            }


            // Get user session data for API calls
            $apiUser = session('api_user');
            $apiToken = session('api_token');
            $userId = $apiUser['id'] ?? null;


            //Get RW from Api
            $response = Http::withToken($apiToken)
                ->get($this->getApiUrl() . '/rw/' . $rw_uuid);
            $response = $response->json();
            $rwApi = $response['data'];

            $uploadedFiles = $request->file('kk_images');

            if (empty($uploadedFiles)) {
                return redirect()->route('rw.index', [$desa_id, $rw_id])->with('success', 'No files were uploaded.');
            }

            // Create temp directory if it doesn't exist
            if (!Storage::disk('local')->exists('temp_kk_uploads')) {
                Storage::disk('local')->makeDirectory('temp_kk_uploads');
            }

            // Process and store each image
            $jobs = [];
            $savedFiles = [];

            foreach ($uploadedFiles as $file) {
                $originalFilename = $file->getClientOriginalName();

                // Use original filename directly
                $filename = $originalFilename;

                // Check if file with same name already exists, add timestamp if needed
                $counter = 1;
                $baseName = pathinfo($originalFilename, PATHINFO_FILENAME);
                $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);

                while (Storage::disk('local')->exists('temp_kk_uploads/' . $filename)) {
                    $filename = $baseName . '_' . $counter . '.' . $extension;
                    $counter++;
                }

                // Store file in temp directory with original name (or modified if duplicate)
                $filePath = $file->storeAs('temp_kk_uploads', $filename, 'local');

                $savedFiles[] = [
                    'path' => $filePath,
                    'filename' => $filename,
                    'original_filename' => $originalFilename
                ];

                // Create job data
                $jobData = [
                    'filename' => $filename,
                    'original_filename' => $originalFilename,
                    'file_path' => $filePath,
                    'raw' => null // Will be populated after OCR
                ];

                $jobs[] = new \App\Jobs\ProcessKKDataJob($jobData, $desa_id, $rw_id, $userId, $apiToken);
            }
            if (empty($jobs)) {
                // Clean up uploaded files
                foreach ($savedFiles as $savedFile) {
                    Storage::disk('local')->delete($savedFile['path']);
                }
                return back()->withErrors(['kk_images' => 'No valid image files found.']);
            }

            // Dispatch jobs in batches
            $batch = \Illuminate\Support\Facades\Bus::batch($jobs)
                ->then(function (\Illuminate\Bus\Batch $batch) use ($desa_id, $rw_id) {
                    // Update job status to completed
                    \App\Models\RwJobStatus::where('batch_id', $batch->id)->update([
                        'status' => 'completed',
                        'processed_jobs' => $batch->processedJobs(),
                        'failed_jobs' => $batch->failedJobs,
                        'completed_at' => now()
                    ]);

                    \Illuminate\Support\Facades\Log::info('All KK image processing jobs completed', [
                        'batch_id' => $batch->id,
                        'rw_id' => $rw_id,
                        'total_jobs' => $batch->totalJobs
                    ]);
                })
                ->catch(function (\Illuminate\Bus\Batch $batch, \Throwable $e) use ($desa_id, $rw_id) {
                    // Update job status to failed
                    \App\Models\RwJobStatus::where('batch_id', $batch->id)->update([
                        'status' => 'failed',
                        'processed_jobs' => $batch->processedJobs(),
                        'failed_jobs' => $batch->failedJobs,
                        'error_message' => $e->getMessage(),
                        'completed_at' => now()
                    ]);

                    \Illuminate\Support\Facades\Log::error('KK image processing batch failed', [
                        'batch_id' => $batch->id,
                        'rw_id' => $rw_id,
                        'error' => $e->getMessage()
                    ]);
                })
                ->finally(function (\Illuminate\Bus\Batch $batch) use ($desa_id, $rw_id) {
                    \Illuminate\Support\Facades\Log::info('KK image processing batch finished', [
                        'batch_id' => $batch->id,
                        'rw_id' => $rw_id,
                        'processed_jobs' => $batch->processedJobs(),
                        'failed_jobs' => $batch->failedJobs
                    ]);
                })
                ->allowFailures()
                ->onConnection('database')
                ->dispatch();

            // Create job status record
            \App\Models\RwJobStatus::create([
                'rw_id' => $rw_id,
                'batch_id' => $batch->id,
                'status' => 'processing',
                'total_jobs' => count($jobs),
                'started_at' => now()
            ]);
            return redirect()->route('rw.index', [$desa_id, $rwApi['id']])
                ->with('success', 'Images uploaded successfully. Processing ' . count($jobs) . ' files in background. Batch ID: ' . $batch->id);

        } catch (\Exception $e) {
            // Clean up any uploaded files on error
            if (isset($savedFiles)) {
                foreach ($savedFiles as $savedFile) {
                    Storage::disk('local')->delete($savedFile['path']);
                }
            }

            \Illuminate\Support\Facades\Log::error('Image upload processing failed', [
                'error' => $e->getMessage(),
                'rw_id' => $rw_id
            ]);

            return back()->withErrors(['kk_images' => 'Error processing files: ' . $e->getMessage()]);
        }
    }
}
