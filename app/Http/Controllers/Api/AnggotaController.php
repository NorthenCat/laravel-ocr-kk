<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\KK;
use App\Models\RW;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AnggotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $desa_id, $rw_id, $kk_id)
    {
        try {
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            // Check desa access
            $desa = Desa::find($desa_id);
            if (!$desa || !$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            $kk = KK::where('rw_id', $rw_id)->findOrFail($kk_id);
            $anggota = Anggota::with('getKk.getRw.getDesa')
                ->where('kk_id', $kk->id)
                ->orderBy('nama_lengkap', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $anggota
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch Anggota data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $desa_id, $rw_id, $kk_id)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User ID is required'
            ], 400);
        }

        // Check desa access
        $desa = Desa::find($desa_id);
        if (!$desa || !$desa->hasAccess($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied to this desa'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $kk = KK::where('rw_id', $rw_id)->findOrFail($kk_id);

            $anggota = Anggota::create([
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

            return response()->json([
                'success' => true,
                'message' => 'Anggota created successfully',
                'data' => $anggota->load('getKk.getRw.getDesa')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Anggota',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $desa_id, $rw_id, $kk_id, $anggota_id)
    {
        try {
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            // Check desa access
            $desa = Desa::find($desa_id);
            if (!$desa || !$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            $anggota = Anggota::with('getKk.getRw.getDesa')
                ->whereHas('getKk', function ($query) use ($rw_id) {
                    $query->where('rw_id', $rw_id);
                })
                ->where('kk_id', $kk_id)
                ->findOrFail($anggota_id);

            return response()->json([
                'success' => true,
                'data' => $anggota
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Anggota not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $desa_id, $rw_id, $kk_id, $anggota_id)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User ID is required'
            ], 400);
        }

        // Check desa access
        $desa = Desa::find($desa_id);
        if (!$desa || !$desa->hasAccess($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied to this desa'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $anggota = Anggota::whereHas('getKk', function ($query) use ($rw_id) {
                $query->where('rw_id', $rw_id);
            })
                ->where('kk_id', $kk_id)
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

            return response()->json([
                'success' => true,
                'message' => 'Anggota updated successfully',
                'data' => $anggota->load('getKk.getRw.getDesa')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Anggota',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $desa_id, $rw_id, $kk_id, $anggota_id)
    {
        try {
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            // Check desa access
            $desa = Desa::find($desa_id);
            if (!$desa || !$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            $anggota = Anggota::whereHas('getKk', function ($query) use ($rw_id) {
                $query->where('rw_id', $rw_id);
            })
                ->where('kk_id', $kk_id)
                ->findOrFail($anggota_id);

            $anggota->delete();

            return response()->json([
                'success' => true,
                'message' => 'Anggota deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Anggota',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display standalone anggota
     */
    public function showStandalone(Request $request, $desa_id, $rw_id, $anggota_id)
    {
        try {
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            // Check desa access
            $desa = Desa::find($desa_id);
            if (!$desa || !$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            $anggota = Anggota::with('getKk.getRw.getDesa')
                ->whereHas('getKk', function ($query) use ($rw_id) {
                    $query->where('rw_id', $rw_id)
                        ->where('no_kk', '0000000000000000');
                })
                ->findOrFail($anggota_id);

            return response()->json([
                'success' => true,
                'data' => $anggota
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Standalone Anggota not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update standalone anggota
     */
    public function updateStandalone(Request $request, $desa_id, $rw_id, $anggota_id)
    {
        $userId = $request->input('user_id');
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User ID is required'
            ], 400);
        }

        // Check desa access
        $desa = Desa::find($desa_id);
        if (!$desa || !$desa->hasAccess($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied to this desa'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $anggota = Anggota::whereHas('getKk', function ($query) use ($rw_id) {
                $query->where('rw_id', $rw_id)
                    ->where('no_kk', '0000000000000000');
            })
                ->findOrFail($anggota_id);

            $rw = RW::where('desa_id', $desa_id)->findOrFail($rw_id);

            // If assigning to existing KK
            if ($request->assign_to_kk) {
                $kk = KK::where('rw_id', $rw_id)
                    ->where('no_kk', '!=', '0000000000000000') // Prevent assignment to zero KK
                    ->findOrFail($request->assign_to_kk);
                $anggota->update(['kk_id' => $kk->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Anggota berhasil dipindahkan ke KK ' . $kk->no_kk,
                    'data' => $anggota->load('getKk.getRw.getDesa')
                ]);
            }

            // If creating new KK
            if ($request->no_kk) {
                // Prevent creating KK with 16 zeros manually
                if ($request->no_kk === '0000000000000000') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nomor KK tidak boleh 16 angka nol.'
                    ], 422);
                }

                // Check if KK number already exists
                $existingKK = KK::where('rw_id', $rw->id)->where('no_kk', $request->no_kk)->first();
                if ($existingKK) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nomor KK sudah terdaftar di RW ini.'
                    ], 422);
                }

                // Create new KK
                $kk = KK::create([
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'no_kk' => $request->no_kk,
                    'nama_kepala_keluarga' => $request->nama_lengkap,
                    'rw_id' => $rw->id,
                ]);

                $anggota->update(['kk_id' => $kk->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'KK baru berhasil dibuat dan anggota dipindahkan.',
                    'data' => $anggota->load('getKk.getRw.getDesa')
                ]);
            }

            // Update anggota data only
            $anggota->update($request->except(['user_id', 'assign_to_kk', 'no_kk']));

            return response()->json([
                'success' => true,
                'message' => 'Standalone Anggota updated successfully',
                'data' => $anggota->load('getKk.getRw.getDesa')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update standalone Anggota',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete standalone anggota
     */
    public function destroyStandalone(Request $request, $desa_id, $rw_id, $anggota_id)
    {
        try {
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            // Check desa access
            $desa = Desa::find($desa_id);
            if (!$desa || !$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            $anggota = Anggota::whereHas('getKk', function ($query) use ($rw_id) {
                $query->where('rw_id', $rw_id)
                    ->where('no_kk', '0000000000000000');
            })
                ->findOrFail($anggota_id);

            $anggota->delete();

            return response()->json([
                'success' => true,
                'message' => 'Standalone Anggota deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete standalone Anggota',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
