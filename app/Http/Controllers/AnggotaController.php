<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\KK;
use App\Models\RW;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AnggotaController extends Controller
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
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($desa_id, $rw_id, $kk_id)
    {
        $token = $this->getApiToken();
        $userId = $this->getUserId();

        if ($token && $userId) {
            try {
                $response = Http::withToken($token)
                    ->timeout(30)
                    ->get($this->getApiUrl() . "/desa/{$desa_id}/rw/{$rw_id}/kk/{$kk_id}", [
                        'user_id' => $userId
                    ]);

                if ($response->successful()) {
                    $apiData = $response->json();
                    if ($apiData['success']) {
                        // Convert API response to object for view compatibility
                        $kk = (object) $apiData['data'];
                        if (isset($kk->get_rw)) {
                            $kk->getRw = (object) $kk->get_rw;
                            if (isset($kk->get_rw['get_desa'])) {
                                $kk->getRw->getDesa = (object) $kk->get_rw['get_desa'];
                            }
                        }

                        return view('anggota.form', compact('kk'));
                    }
                }
            } catch (\Exception $e) {
                // API failed, fall back to local data
            }
        }

        // Fallback to local data
        $kk = KK::with('getRw.getDesa')->where('rw_id', $rw_id)->findOrFail($kk_id);
        return view('anggota.form', compact('kk'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $desa_id, $rw_id, $kk_id)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20|unique:kk_members,nik',
            'jenis_kelamin' => 'required|in:LAKI-LAKI,PEREMPUAN',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:100',
            'pendidikan' => 'nullable|string|max:100',
            'jenis_pekerjaan' => 'nullable|string|max:100',
            'golongan_darah' => 'nullable|string|max:10',
            'status_perkawinan' => 'nullable|string|max:50',
            'status_hubungan_dalam_keluarga' => 'nullable|string|max:50',
            'kewarganegaraan' => 'nullable|string|max:10',
            'no_paspor' => 'nullable|string|max:20',
            'no_kitap' => 'nullable|string|max:20',
            'ayah' => 'nullable|string|max:255',
            'ibu' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'kode_pos' => 'nullable|string|max:10',
            'desa_kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten_kota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kk_disahkan_tanggal' => 'nullable|date',
        ]);

        $token = $this->getApiToken();
        $userId = $this->getUserId();

        if ($token && $userId) {
            try {
                $requestData = $request->all();
                $requestData['user_id'] = $userId;

                $response = Http::withToken($token)
                    ->timeout(30)
                    ->post($this->getApiUrl() . "/desa/{$desa_id}/rw/{$rw_id}/kk/{$kk_id}/anggota", $requestData);

                if ($response->successful()) {
                    $apiData = $response->json();
                    if ($apiData['success']) {
                        return redirect()->route('kk.index', [$desa_id, $rw_id, $kk_id])
                            ->with('success', 'Anggota keluarga berhasil ditambahkan.');
                    } else {
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'API Error: ' . ($apiData['message'] ?? 'Unknown error'));
                    }
                } else {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'API request failed with status: ' . $response->status());
                }
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Connection error: ' . $e->getMessage());
            }
        }

        // Fallback to local operation
        $kk = KK::where('rw_id', $rw_id)->findOrFail($kk_id);

        Anggota::create([
            'kk_id' => $kk->id,
            'nama_lengkap' => $request->nama_lengkap,
            'nik' => $request->nik,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'agama' => $request->agama,
            'pendidikan' => $request->pendidikan,
            'jenis_pekerjaan' => $request->jenis_pekerjaan,
            'golongan_darah' => $request->golongan_darah,
            'status_perkawinan' => $request->status_perkawinan,
            'status_hubungan_dalam_keluarga' => $request->status_hubungan_dalam_keluarga,
            'kewarganegaraan' => $request->kewarganegaraan,
            'no_paspor' => $request->no_paspor,
            'no_kitap' => $request->no_kitap,
            'ayah' => $request->ayah,
            'ibu' => $request->ibu,
            'alamat' => $request->alamat,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'kode_pos' => $request->kode_pos,
            'desa_kelurahan' => $request->desa_kelurahan,
            'kecamatan' => $request->kecamatan,
            'kabupaten_kota' => $request->kabupaten_kota,
            'provinsi' => $request->provinsi,
            'kk_disahkan_tanggal' => $request->kk_disahkan_tanggal,
        ]);

        return redirect()->route('kk.index', [$desa_id, $rw_id, $kk_id])
            ->with('success', 'Anggota keluarga berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($desa_id, $rw_id, $kk_id, $anggota_id)
    {
        $token = $this->getApiToken();
        $userId = $this->getUserId();

        if ($token && $userId) {
            try {
                $response = Http::withToken($token)
                    ->timeout(30)
                    ->get($this->getApiUrl() . "/desa/{$desa_id}/rw/{$rw_id}/kk/{$kk_id}/anggota/{$anggota_id}", [
                        'user_id' => $userId
                    ]);

                if ($response->successful()) {
                    $apiData = $response->json();
                    if ($apiData['success']) {
                        // Convert API response to object for view compatibility
                        $anggota = (object) $apiData['data'];
                        if (isset($anggota->get_kk)) {
                            $anggota->getKk = (object) $anggota->get_kk;
                            if (isset($anggota->get_kk['get_rw'])) {
                                $anggota->getKk->getRw = (object) $anggota->get_kk['get_rw'];
                                if (isset($anggota->get_kk['get_rw']['get_desa'])) {
                                    $anggota->getKk->getRw->getDesa = (object) $anggota->get_kk['get_rw']['get_desa'];
                                }
                            }
                        }

                        return view('anggota.form', compact('anggota'));
                    }
                }
            } catch (\Exception $e) {
                // API failed, fall back to local data
            }
        }

        // Fallback to local data
        $anggota = Anggota::with('getKk.getRw.getDesa')
            ->whereHas('getKk', function ($query) use ($rw_id) {
                $query->where('rw_id', $rw_id);
            })
            ->findOrFail($anggota_id);

        return view('anggota.form', compact('anggota'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $desa_id, $rw_id, $kk_id, $anggota_id)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20|unique:kk_members,nik,' . $anggota_id,
            'jenis_kelamin' => 'required|in:LAKI-LAKI,PEREMPUAN',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:100',
            'pendidikan' => 'nullable|string|max:100',
            'jenis_pekerjaan' => 'nullable|string|max:100',
            'golongan_darah' => 'nullable|string|max:10',
            'status_perkawinan' => 'nullable|string|max:50',
            'status_hubungan_dalam_keluarga' => 'nullable|string|max:50',
            'kewarganegaraan' => 'nullable|string|max:10',
            'no_paspor' => 'nullable|string|max:20',
            'no_kitap' => 'nullable|string|max:20',
            'ayah' => 'nullable|string|max:255',
            'ibu' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'kode_pos' => 'nullable|string|max:10',
            'desa_kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten_kota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kk_disahkan_tanggal' => 'nullable|date',
        ]);

        $token = $this->getApiToken();
        $userId = $this->getUserId();

        if ($token && $userId) {
            try {
                $requestData = $request->all();
                $requestData['user_id'] = $userId;

                $response = Http::withToken($token)
                    ->timeout(30)
                    ->put($this->getApiUrl() . "/desa/{$desa_id}/rw/{$rw_id}/kk/{$kk_id}/anggota/{$anggota_id}", $requestData);

                if ($response->successful()) {
                    $apiData = $response->json();
                    if ($apiData['success']) {
                        return redirect()->route('kk.index', [$desa_id, $rw_id, $kk_id])
                            ->with('success', 'Anggota keluarga berhasil diperbarui.');
                    } else {
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'API Error: ' . ($apiData['message'] ?? 'Unknown error'));
                    }
                } else {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'API request failed with status: ' . $response->status());
                }
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Connection error: ' . $e->getMessage());
            }
        }

        // Fallback to local operation
        $anggota = Anggota::with('getKk')
            ->whereHas('getKk', function ($query) use ($rw_id) {
                $query->where('rw_id', $rw_id);
            })
            ->findOrFail($anggota_id);

        $anggota->update([
            'nama_lengkap' => $request->nama_lengkap,
            'nik' => $request->nik,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'agama' => $request->agama,
            'pendidikan' => $request->pendidikan,
            'jenis_pekerjaan' => $request->jenis_pekerjaan,
            'golongan_darah' => $request->golongan_darah,
            'status_perkawinan' => $request->status_perkawinan,
            'status_hubungan_dalam_keluarga' => $request->status_hubungan_dalam_keluarga,
            'kewarganegaraan' => $request->kewarganegaraan,
            'no_paspor' => $request->no_paspor,
            'no_kitap' => $request->no_kitap,
            'ayah' => $request->ayah,
            'ibu' => $request->ibu,
            'alamat' => $request->alamat,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'kode_pos' => $request->kode_pos,
            'desa_kelurahan' => $request->desa_kelurahan,
            'kecamatan' => $request->kecamatan,
            'kabupaten_kota' => $request->kabupaten_kota,
            'provinsi' => $request->provinsi,
            'kk_disahkan_tanggal' => $request->kk_disahkan_tanggal,
        ]);

        return redirect()->route('kk.index', [$desa_id, $rw_id, $kk_id])
            ->with('success', 'Anggota keluarga berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($desa_id, $rw_id, $kk_id, $anggota_id)
    {
        $token = $this->getApiToken();
        $userId = $this->getUserId();

        if ($token && $userId) {
            try {
                $response = Http::withToken($token)
                    ->timeout(30)
                    ->delete($this->getApiUrl() . "/desa/{$desa_id}/rw/{$rw_id}/kk/{$kk_id}/anggota/{$anggota_id}", [
                        'user_id' => $userId
                    ]);

                if ($response->successful()) {
                    $apiData = $response->json();
                    if ($apiData['success']) {
                        return redirect()->route('kk.index', [$desa_id, $rw_id, $kk_id])
                            ->with('success', 'Anggota keluarga berhasil dihapus.');
                    } else {
                        return redirect()->back()
                            ->with('error', 'API Error: ' . ($apiData['message'] ?? 'Unknown error'));
                    }
                } else {
                    return redirect()->back()
                        ->with('error', 'API request failed with status: ' . $response->status());
                }
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Connection error: ' . $e->getMessage());
            }
        }

        // Fallback to local operation
        $anggota = Anggota::with('getKk')
            ->whereHas('getKk', function ($query) use ($rw_id) {
                $query->where('rw_id', $rw_id);
            })
            ->findOrFail($anggota_id);

        $anggota->delete();

        return redirect()->route('kk.index', [$desa_id, $rw_id, $kk_id])
            ->with('success', 'Anggota keluarga berhasil dihapus.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editStandalone($desa_id, $rw_id, $anggota_id)
    {
        $token = $this->getApiToken();
        $userId = $this->getUserId();

        if ($token && $userId) {
            try {
                // Get RW data with KK list from API
                $rwResponse = Http::withToken($token)
                    ->timeout(30)
                    ->get($this->getApiUrl() . "/desa/{$desa_id}/rw/{$rw_id}", [
                        'user_id' => $userId
                    ]);

                // Get standalone anggota data from API
                $anggotaResponse = Http::withToken($token)
                    ->timeout(30)
                    ->get($this->getApiUrl() . "/desa/{$desa_id}/rw/{$rw_id}/standalone/{$anggota_id}", [
                        'user_id' => $userId
                    ]);


                if ($rwResponse->successful() && $anggotaResponse->successful()) {
                    $rwData = $rwResponse->json();
                    $anggotaData = $anggotaResponse->json();

                    if ($rwData['success'] && $anggotaData['success']) {
                        // Convert API response to objects for view compatibility
                        $rw = (object) $rwData['data']['rw'];
                        if (isset($rw->get_desa)) {
                            $rw->getDesa = (object) $rw->get_desa;
                        }
                        if (isset($rw->get_k_k)) {
                            $rw->getKK = collect($rw->get_k_k)->map(function ($kk) {
                                return (object) $kk;
                            });
                        }

                        $anggota = (object) $anggotaData['data'];


                        // Prepare KK data for JavaScript
                        $kkData = $rw->getKK->filter(function ($kk) {
                            return $kk->no_kk !== '0000000000000000';
                        })->map(function ($kk) {
                            return [
                                'id' => $kk->id,
                                'no_kk' => $kk->no_kk,
                                'nama_kepala_keluarga' => $kk->nama_kepala_keluarga,
                                'search_text' => strtolower("{$kk->no_kk} {$kk->nama_kepala_keluarga}")
                            ];
                        })->values();

                        return view('anggota.standalone-form', compact('rw', 'anggota', 'kkData'));
                    } else {
                        return redirect()->back()
                            ->with('error', 'API Error: ' . ($rwData['message'] ?? $anggotaData['message'] ?? 'Unknown error'));
                    }
                } else {
                    return redirect()->back()
                        ->with('error', 'API request failed with status: ' . ($rwResponse->status() . '/' . $anggotaResponse->status()));
                }
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Connection error: ' . $e->getMessage());
            }
        }

        // Fallback to local data
        $rw = RW::with([
            'getDesa',
            'getKK' => function ($query) {
                $query->where('no_kk', '!=', '0000000000000000') // Exclude zero KK from assignment options
                    ->orderBy('no_kk', 'asc');
            }
        ])->where('desa_id', $desa_id)->findOrFail($rw_id);

        // Find anggota that belongs to zero KK only
        $anggota = Anggota::whereHas('getKk', function ($query) use ($rw_id) {
            $query->where('rw_id', $rw_id)
                ->where('no_kk', '0000000000000000');
        })->findOrFail($anggota_id);

        // Prepare KK data for JavaScript
        $kkData = $rw->getKK->map(function ($kk) {
            return [
                'id' => $kk->id,
                'no_kk' => $kk->no_kk,
                'nama_kepala_keluarga' => $kk->nama_kepala_keluarga,
                'search_text' => strtolower("{$kk->no_kk} {$kk->nama_kepala_keluarga}")
            ];
        })->values();

        return view('anggota.standalone-form', compact('rw', 'anggota', 'kkData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStandalone(Request $request, $desa_id, $rw_id, $anggota_id)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20|unique:kk_members,nik,' . $anggota_id,
            'jenis_kelamin' => 'required|in:LAKI-LAKI,PEREMPUAN',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:100',
            'pendidikan' => 'nullable|string|max:100',
            'jenis_pekerjaan' => 'nullable|string|max:100',
            'golongan_darah' => 'nullable|string|max:10',
            'status_perkawinan' => 'nullable|string|max:50',
            'status_hubungan_dalam_keluarga' => 'nullable|string|max:50',
            'kewarganegaraan' => 'nullable|string|max:10',
            'no_paspor' => 'nullable|string|max:20',
            'no_kitap' => 'nullable|string|max:20',
            'ayah' => 'nullable|string|max:255',
            'ibu' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'kode_pos' => 'nullable|string|max:10',
            'desa_kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten_kota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'kk_disahkan_tanggal' => 'nullable|date',
            'no_kk' => 'nullable|string|max:20',
            'assign_to_kk' => 'nullable|exists:kk,id',
        ]);

        $token = $this->getApiToken();
        $userId = $this->getUserId();

        if ($token && $userId) {
            try {
                $requestData = $request->all();
                $requestData['user_id'] = $userId;

                $response = Http::withToken($token)
                    ->timeout(30)
                    ->put($this->getApiUrl() . "/desa/{$desa_id}/rw/{$rw_id}/standalone/{$anggota_id}", $requestData);

                if ($response->successful()) {
                    $apiData = $response->json();
                    if ($apiData['success']) {
                        $message = 'Data anggota berhasil diperbarui.';
                        if ($request->assign_to_kk) {
                            $message = 'Anggota berhasil dipindahkan ke KK.';
                        } else if ($request->no_kk) {
                            $message = 'KK baru berhasil dibuat dan anggota dipindahkan.';
                        }

                        return redirect()->route('rw.index', [$desa_id, $rw_id])
                            ->with('success', $message);
                    } else {
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'API Error: ' . ($apiData['message'] ?? 'Unknown error'));
                    }
                } else {
                    $errors = $response->json()['errors'] ?? [];
                    $errorMessage = 'API request failed with status: ' . $response->status();
                    if (!empty($errors)) {
                        $errorMessage .= ' - ' . collect($errors)->flatten()->implode(' ');
                    }
                    return redirect()->back()
                        ->withInput()
                        ->with('error', $errorMessage);
                }
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Connection error: ' . $e->getMessage());
            }
        }

        // Fallback to local operation
        // Find anggota that belongs to zero KK only
        $anggota = Anggota::whereHas('getKk', function ($query) use ($rw_id) {
            $query->where('rw_id', $rw_id)
                ->where('no_kk', '0000000000000000');
        })->findOrFail($anggota_id);

        $rw = RW::where('desa_id', $desa_id)->findOrFail($rw_id);

        // If assigning to existing KK
        if ($request->assign_to_kk) {
            $kk = KK::where('rw_id', $rw_id)
                ->where('no_kk', '!=', '0000000000000000') // Prevent assignment to zero KK
                ->findOrFail($request->assign_to_kk);
            $anggota->update(['kk_id' => $kk->id]);

            return redirect()->route('rw.index', [$desa_id, $rw_id])
                ->with('success', 'Anggota berhasil dipindahkan ke KK ' . $kk->no_kk);
        }

        // If creating new KK
        if ($request->no_kk) {
            // Prevent creating KK with 16 zeros manually
            if ($request->no_kk === '0000000000000000') {
                return back()->withInput()->withErrors(['no_kk' => 'Nomor KK tidak boleh 16 angka nol.']);
            }

            // Check if KK number already exists
            $existingKK = KK::where('rw_id', $rw->id)->where('no_kk', $request->no_kk)->first();
            if ($existingKK) {
                return back()->withInput()->withErrors(['no_kk' => 'Nomor KK sudah terdaftar di RW ini.']);
            }

            // Create new KK
            $kk = KK::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'no_kk' => $request->no_kk,
                'nama_kepala_keluarga' => $request->nama_lengkap,
                'rw_id' => $rw->id,
            ]);

            $anggota->update(['kk_id' => $kk->id]);

            return redirect()->route('rw.index', [$desa_id, $rw_id])
                ->with('success', 'KK baru berhasil dibuat dan anggota dipindahkan.');
        }

        // Update anggota data only
        $anggota->update([
            'nama_lengkap' => $request->nama_lengkap,
            'nik' => $request->nik,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'agama' => $request->agama,
            'pendidikan' => $request->pendidikan,
            'jenis_pekerjaan' => $request->jenis_pekerjaan,
            'golongan_darah' => $request->golongan_darah,
            'status_perkawinan' => $request->status_perkawinan,
            'status_hubungan_dalam_keluarga' => $request->status_hubungan_dalam_keluarga,
            'kewarganegaraan' => $request->kewarganegaraan,
            'no_paspor' => $request->no_paspor,
            'no_kitap' => $request->no_kitap,
            'ayah' => $request->ayah,
            'ibu' => $request->ibu,
            'alamat' => $request->alamat,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'kode_pos' => $request->kode_pos,
            'desa_kelurahan' => $request->desa_kelurahan,
            'kecamatan' => $request->kecamatan,
            'kabupaten_kota' => $request->kabupaten_kota,
            'provinsi' => $request->provinsi,
            'kk_disahkan_tanggal' => $request->kk_disahkan_tanggal,
        ]);

        return redirect()->route('rw.index', [$desa_id, $rw_id])
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroyStandalone($desa_id, $rw_id, $anggota_id)
    {
        $token = $this->getApiToken();
        $userId = $this->getUserId();

        if ($token && $userId) {
            try {
                $response = Http::withToken($token)
                    ->timeout(30)
                    ->delete($this->getApiUrl() . "/desa/{$desa_id}/rw/{$rw_id}/standalone/{$anggota_id}", [
                        'user_id' => $userId
                    ]);

                if ($response->successful()) {
                    $apiData = $response->json();
                    if ($apiData['success']) {
                        return redirect()->route('rw.index', [$desa_id, $rw_id])
                            ->with('success', 'Anggota berhasil dihapus.');
                    } else {
                        return redirect()->back()
                            ->with('error', 'API Error: ' . ($apiData['message'] ?? 'Unknown error'));
                    }
                } else {
                    return redirect()->back()
                        ->with('error', 'API request failed with status: ' . $response->status());
                }
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Connection error: ' . $e->getMessage());
            }
        }

        // Fallback to local operation
        // Find anggota that belongs to zero KK only
        $anggota = Anggota::whereHas('getKk', function ($query) use ($rw_id) {
            $query->where('rw_id', $rw_id)
                ->where('no_kk', '0000000000000000');
        })->findOrFail($anggota_id);

        $anggota->delete();

        return redirect()->route('rw.index', [$desa_id, $rw_id])
            ->with('success', 'Anggota berhasil dihapus.');
    }
}
