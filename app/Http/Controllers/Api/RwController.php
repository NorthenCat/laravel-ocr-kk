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
    public function index($desa_id)
    {
        try {
            $desa = Desa::findOrFail($desa_id);
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
    public function show($desa_id, $rw_id)
    {
        try {
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
    public function exportExcel($desa_id, $rw_id)
    {
        try {
            $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);

            $fileName = 'KK-OCR_' . $rw->getDesa->nama_desa . '_' . $rw->nama_rw . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new AnggotaExport($rw_id, true), $fileName);
        } catch (\Exception $e) {
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
    public function exportExcelWithoutFilename($desa_id, $rw_id)
    {
        try {
            $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);

            $fileName = 'KK-OCR_NoFilename_' . $rw->getDesa->nama_desa . '_' . $rw->nama_rw . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new AnggotaExport($rw_id, false), $fileName);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export Excel',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
