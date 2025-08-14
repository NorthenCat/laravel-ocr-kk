<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\KK;
use App\Models\RW;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AnggotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($desa_id, $rw_id, $kk_id)
    {
        try {
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
    public function show($desa_id, $rw_id, $kk_id, $anggota_id)
    {
        try {
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
    public function destroy($desa_id, $rw_id, $kk_id, $anggota_id)
    {
        try {
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
    public function showStandalone($desa_id, $rw_id, $anggota_id)
    {
        try {
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
                $query->where('rw_id', $rw_id)
                    ->where('no_kk', '0000000000000000');
            })
                ->findOrFail($anggota_id);

            $anggota->update($request->all());

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
    public function destroyStandalone($desa_id, $rw_id, $anggota_id)
    {
        try {
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
