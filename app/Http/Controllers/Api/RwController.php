<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\RW;
use App\Models\Desa;
use App\Exports\AnggotaExport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class RwController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $desa_id)
    {
        try {
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            $desa = Desa::findOrFail($desa_id);

            // Check if user has access to this desa
            if (!$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            $rws = RW::with([
                'getDesa',
                'getKK.getWarga',
                'getKK' => function ($query) {
                    $query->orderBy('no_kk', 'asc');
                },
                'getCurrentJobStatus',
                'getJobStatus' => function ($query) {
                    $query->orderBy('created_at', 'desc')->limit(5);
                }
            ])->where('desa_id', $desa_id)->withCount(['getKK', 'getWarga'])->get();

            return response()->json([
                'success' => true,
                'data' => $rws
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch RW data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $desa_id)
    {
        $validator = Validator::make($request->all(), [
            'nama_rw' => 'required|string|max:255',
            'google_drive' => 'nullable|url|max:500',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $desa = Desa::findOrFail($desa_id);

            // Check if user has access to this desa
            if (!$desa->hasAccess($request->user_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            $rw = RW::create([
                'uuid' => Str::uuid(),
                'nama_rw' => $request->nama_rw,
                'google_drive' => $request->google_drive,
                'desa_id' => $desa->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'RW created successfully',
                'data' => $rw->load(['getDesa', 'getKK'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create RW',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $desa_id, $rw_id)
    {
        try {
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            $desa = Desa::findOrFail($desa_id);

            // Check if user has access to this desa
            if (!$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

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

            return response()->json([
                'success' => true,
                'data' => [
                    'rw' => $rw,
                    'standalone_anggota' => $standaloneAnggota,
                    'anggota_tanpa_nik' => $anggotaTanpaNik,
                    'failed_files' => $failedFiles,
                    'total_rt' => $totalRT
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'RW not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $desa_id, $rw_id)
    {
        $validator = Validator::make($request->all(), [
            'nama_rw' => 'required|string|max:255',
            'google_drive' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $rw = RW::where('desa_id', $desa_id)->findOrFail($rw_id);
            $rw->update([
                'nama_rw' => $request->nama_rw,
                'google_drive' => $request->google_drive,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'RW updated successfully',
                'data' => $rw->load(['getDesa', 'getKK'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update RW',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($desa_id, $rw_id)
    {
        try {
            $rw = RW::where('desa_id', $desa_id)->findOrFail($rw_id);
            $rw->delete();

            return response()->json([
                'success' => true,
                'message' => 'RW deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete RW',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export to Excel.
     */
    public function exportExcel(Request $request, $desa_id, $rw_id)
    {
        try {
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            $desa = Desa::findOrFail($desa_id);

            // Check if user has access to this desa
            if (!$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);

            $sanitizedDesaName = preg_replace('/[\/\\\\]/', '_', $rw->getDesa->nama_desa);
            $sanitizedRwName = preg_replace('/[\/\\\\]/', '_', $rw->nama_rw);
            $fileName = "KK-OCR_{$sanitizedDesaName}_{$sanitizedRwName}_" . now()->format('Y-m-d_H-i-s') . '.xlsx';

            // Return the Excel file as download response
            return Excel::download(new AnggotaExport($rw_id, true), $fileName, \Maatwebsite\Excel\Excel::XLSX, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\""
            ]);

        } catch (\Exception $e) {
            \Log::error('API Export Excel failed', [
                'desa_id' => $desa_id,
                'rw_id' => $rw_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to export Excel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export to Excel without filename.
     */
    public function exportExcelWithoutFilename(Request $request, $desa_id, $rw_id)
    {
        try {
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            $desa = Desa::findOrFail($desa_id);

            // Check if user has access to this desa
            if (!$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);

            $fileName = "KK-OCR_NoFilename_{$rw->getDesa->nama_desa}_{$rw->nama_rw}_" . now()->format('Y-m-d_H-i-s') . '.xlsx';

            // Return the Excel file as download response (without filename in the export data)
            return Excel::download(new AnggotaExport($rw_id, false), $fileName, \Maatwebsite\Excel\Excel::XLSX, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\""
            ]);

        } catch (\Exception $e) {
            \Log::error('API Export Excel Without Filename failed', [
                'desa_id' => $desa_id,
                'rw_id' => $rw_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to export Excel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process OCR data from N8N
     */
    public function processOcr(Request $request, $desa_id, $rw_id)
    {
        try {
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            $desa = Desa::findOrFail($desa_id);

            // Check if user has access to this desa
            if (!$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            // Validate RW belongs to this desa
            $rw = RW::where('desa_id', $desa_id)->findOrFail($rw_id);

            // Validate request data
            $validator = Validator::make($request->all(), [
                'filename' => 'required|string',
                'n8n_response' => 'required|array',
                'batch_id' => 'required|string',
                'processed_at' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $request->input('n8n_response');

            \Log::info('Received OCR data from job in API server', [
                'filename' => $request->input('filename'),
                'rw_id' => $rw_id,
                'desa_id' => $desa_id,
                'batch_id' => $request->input('batch_id'),
                'isKK' => $data['isKK'] ?? 'not_set',
                'has_anggota' => isset($data['AnggotaKeluarga']),
                'anggota_count' => isset($data['AnggotaKeluarga']) ? count($data['AnggotaKeluarga']) : 0,
            ]);

            // Validate response structure
            if (!isset($data['isKK']) || $data['isKK'] !== true) {
                return response()->json([
                    'success' => false,
                    'message' => 'N8N response indicates this is not KK data',
                    'data' => [
                        'filename' => $request->input('filename'),
                        'isKK' => $data['isKK'] ?? null,
                        'failure_reason' => 'not_kk'
                    ]
                ], 400);
            }

            // Check for AnggotaKeluarga
            if (!isset($data['AnggotaKeluarga']) || empty($data['AnggotaKeluarga'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No AnggotaKeluarga data found in N8N response',
                    'data' => [
                        'filename' => $request->input('filename'),
                        'available_keys' => array_keys($data),
                        'failure_reason' => 'no_anggota_data'
                    ]
                ], 400);
            }

            // //Validate every anggota keluarga nik is unique in database
            // $niks = array_map(function ($anggota) {
            //     return $anggota['NIK'] ?? null;
            // }, $data['AnggotaKeluarga']);

            // // Filter out empty/null NIKs for duplicate checking
            // $validNiks = array_filter($niks, function ($nik) {
            //     return !empty($nik) && $nik !== '-';
            // });

            // // Check for duplicates within the submitted data
            // $duplicateNiks = array_filter(array_count_values($validNiks), function ($count) {
            //     return $count > 1;
            // });

            // if (!empty($duplicateNiks)) {
            //     \Log::warning('Duplicate NIKs found in OCR data in API server', [
            //         'filename' => $request->input('filename'),
            //         'duplicates' => $duplicateNiks,
            //         'batch_id' => $request->input('batch_id')
            //     ]);

            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Duplicate NIKs found in AnggotaKeluarga data',
            //         'data' => [
            //             'filename' => $request->input('filename'),
            //             'duplicate_niks' => array_keys($duplicateNiks),
            //             'failure_reason' => 'duplicate_nik_in_anggota'
            //         ]
            //     ], 400);
            // }

            // // Check for existing NIKs in database
            // if (!empty($validNiks)) {
            //     $existingNiks = Anggota::whereIn('nik', $validNiks)->pluck('nik')->toArray();

            //     if (!empty($existingNiks)) {
            //         \Log::warning('NIKs already exist in database in API server', [
            //             'filename' => $request->input('filename'),
            //             'existing_niks' => $existingNiks,
            //             'batch_id' => $request->input('batch_id')
            //         ]);

            //         return response()->json([
            //             'success' => false,
            //             'message' => 'NIKs already exist in database',
            //             'data' => [
            //                 'filename' => $request->input('filename'),
            //                 'existing_niks' => $existingNiks,
            //                 'failure_reason' => 'nik_already_exists'
            //             ]
            //         ], 409);
            //     }
            // }




            \DB::beginTransaction();

            try {
                // Extract KK data from first anggota (head of family)
                $kkData = $data['AnggotaKeluarga'][0];

                // Check if NoKK exists - if null, create KK with 16 zeros
                if (empty($kkData['NoKK'])) {
                    \Log::info('NoKK is null/empty, creating KK with 16 zeros for standalone anggota', [
                        'filename' => $request->input('filename'),
                        'anggota_count' => count($data['AnggotaKeluarga']),
                        'batch_id' => $request->input('batch_id')
                    ]);

                    $noKK = '0000000000000000'; // 16 zeros

                    // Check if KK with 16 zeros already exists in this RW
                    $existingKK = \App\Models\KK::where('rw_id', $rw->id)->where('no_kk', $noKK)->first();

                    if (!$existingKK) {
                        // Create KK with 16 zeros
                        $kk = \App\Models\KK::create([
                            'uuid' => Str::uuid(),
                            'no_kk' => $noKK,
                            'nama_kepala_keluarga' => 'Anggota Tanpa KK',
                            'rw_id' => $rw->id,
                        ]);

                        \Log::info('KK with 16 zeros created in API server', [
                            'kk_id' => $kk->id,
                            'no_kk' => $kk->no_kk,
                            'batch_id' => $request->input('batch_id')
                        ]);
                    } else {
                        $kk = $existingKK;
                        \Log::info('Using existing KK with 16 zeros in API server', [
                            'kk_id' => $kk->id,
                            'no_kk' => $kk->no_kk,
                            'batch_id' => $request->input('batch_id')
                        ]);
                    }

                    // Create all anggota and assign to the zero KK
                    $anggotaCount = 0;
                    foreach ($data['AnggotaKeluarga'] as $anggotaData) {
                        $anggota = $this->createAnggotaFromOcr($kk, $anggotaData);
                        $anggotaCount++;

                        \Log::info('Anggota created and assigned to zero KK in API server', [
                            'anggota_id' => $anggota->id,
                            'nama_lengkap' => $anggota->nama_lengkap,
                            'nik' => $anggota->nik,
                            'kk_id' => $kk->id,
                            'filename' => $request->input('filename'),
                            'batch_id' => $request->input('batch_id')
                        ]);
                    }

                    \DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'Anggota records created and assigned to zero KK',
                        'data' => [
                            'kk_id' => $kk->id,
                            'no_kk' => $kk->no_kk,
                            'anggota_count' => $anggotaCount,
                            'filename' => $request->input('filename'),
                            'batch_id' => $request->input('batch_id')
                        ]
                    ]);
                }

                $noKK = $kkData['NoKK'];

                // Check if KK already exists
                $existingKK = \App\Models\KK::where('rw_id', $rw->id)->where('no_kk', $noKK)->first();

                if ($existingKK) {
                    \Log::warning('KK already exists in API server, returning error', [
                        'no_kk' => $noKK,
                        'filename' => $request->input('filename'),
                        'batch_id' => $request->input('batch_id')
                    ]);

                    \DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'KK already exists for this RW',
                        'data' => [
                            'no_kk' => $noKK,
                            'filename' => $request->input('filename'),
                            'batch_id' => $request->input('batch_id'),
                            'failure_reason' => 'duplicate_kk'
                        ]
                    ], 409);
                }

                // Create KK
                $kk = \App\Models\KK::create([
                    'uuid' => Str::uuid(),
                    'no_kk' => $noKK,
                    'nama_kepala_keluarga' => $kkData['NamaKepalaKeluarga'] ?? $kkData['NamaLengkap'] ?? 'Unknown',
                    'rw_id' => $rw->id,
                ]);

                \Log::info('KK created successfully in API server', [
                    'kk_id' => $kk->id,
                    'no_kk' => $kk->no_kk,
                    'nama_kepala_keluarga' => $kk->nama_kepala_keluarga,
                    'batch_id' => $request->input('batch_id')
                ]);

                // Create all anggota keluarga
                $anggotaCount = 0;
                foreach ($data['AnggotaKeluarga'] as $anggotaData) {
                    $anggota = $this->createAnggotaFromOcr($kk, $anggotaData);
                    $anggotaCount++;

                    \Log::info('Anggota created in API server', [
                        'anggota_id' => $anggota->id,
                        'nama_lengkap' => $anggota->nama_lengkap,
                        'nik' => $anggota->nik,
                        'kk_id' => $kk->id,
                        'batch_id' => $request->input('batch_id')
                    ]);
                }

                \DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'KK and anggota created from N8N response',
                    'data' => [
                        'kk_id' => $kk->id,
                        'no_kk' => $kk->no_kk,
                        'anggota_count' => $anggotaCount,
                        'filename' => $request->input('filename'),
                        'batch_id' => $request->input('batch_id')
                    ]
                ]);

            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Failed to save KK data to API database', [
                    'error' => $e->getMessage(),
                    'filename' => $request->input('filename'),
                    'batch_id' => $request->input('batch_id'),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process OCR data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create anggota from OCR data
     */
    private function createAnggotaFromOcr(\App\Models\KK $kk, array $anggotaData)
    {
        // Parse tanggal lahir
        $tanggalLahir = null;
        if (!empty($anggotaData['TanggalLahir'])) {
            try {
                $tanggalLahir = \Carbon\Carbon::parse($anggotaData['TanggalLahir']);
            } catch (\Exception $e) {
                \Log::warning('Failed to parse tanggal lahir in API server', [
                    'tanggal_lahir' => $anggotaData['TanggalLahir'],
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Map all fields from N8N response to database
        $anggota = Anggota::create([
            'kk_id' => $kk->id,
            'img_name' => $anggotaData['img_name'] ?? null,
            'nama_kepala_keluarga' => $anggotaData['NamaKepalaKeluarga'] ?? null,
            'alamat' => $anggotaData['Alamat'] ?? null,
            'rt' => $anggotaData['RT'] ?? null,
            'rw' => $anggotaData['RW'] ?? null,
            'kode_pos' => $anggotaData['KodePos'] ?? null,
            'desa_kelurahan' => $anggotaData['DesaKelurahan'] ?? null,
            'kecamatan' => $anggotaData['Kecamatan'] ?? null,
            'kabupaten_kota' => $anggotaData['KabupatenKota'] ?? null,
            'provinsi' => $anggotaData['Provinsi'] ?? null,
            'no_kk' => $anggotaData['NoKK'] ?? null,
            'kk_disahkan_tanggal' => $this->parseDate($anggotaData['KKDisahkanTanggal'] ?? null),
            'nama_lengkap' => $anggotaData['NamaLengkap'] ?? null,
            'nik' => $anggotaData['NIK'] ?? null,
            'jenis_kelamin' => $anggotaData['JenisKelamin'] ?? null,
            'tempat_lahir' => $anggotaData['TempatLahir'] ?? null,
            'tanggal_lahir' => $tanggalLahir,
            'agama' => $anggotaData['Agama'] ?? null,
            'pendidikan' => $anggotaData['Pendidikan'] ?? null,
            'jenis_pekerjaan' => $anggotaData['JenisPekerjaan'] ?? null,
            'golongan_darah' => $anggotaData['GolonganDarah'] ?? null,
            'status_perkawinan' => $anggotaData['StatusPerkawinan'] ?? null,
            'status_hubungan_dalam_keluarga' => $anggotaData['StatusHubunganDalamKeluarga'] ?? null,
            'kewarganegaraan' => $anggotaData['Kewarganegaraan'] ?? null,
            'no_paspor' => $anggotaData['NoPaspor'] ?? null,
            'no_kitap' => $anggotaData['NoKITAP'] ?? null,
            'ayah' => $anggotaData['Ayah'] ?? null,
            'ibu' => $anggotaData['Ibu'] ?? null,
        ]);

        return $anggota;
    }

    /**
     * Parse date string
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($dateString)->toDateString();
        } catch (\Exception $e) {
            \Log::warning('Failed to parse date in API server', [
                'date_string' => $dateString,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function getRw($uuid)
    {
        $rw_uuid = $uuid;

        if (!$rw_uuid) {
            return response()->json([
                'success' => false,
                'message' => 'RW UUID is required'
            ], 400);
        }

        try {
            $rw = RW::with(['getDesa', 'getKK.getWarga'])
                ->where('uuid', $rw_uuid)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $rw
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'RW not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
