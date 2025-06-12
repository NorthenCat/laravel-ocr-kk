<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\KK;
use App\Models\RW;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnggotaController extends Controller
{
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
        ]);

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
        $anggota = Anggota::with('getKk')
            ->whereHas('getKk', function ($query) use ($rw_id) {
                $query->where('rw_id', $rw_id);
            })
            ->findOrFail($anggota_id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20|unique:kk_members,nik,' . $anggota->id,
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
        ]);

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
        ]);

        return redirect()->route('kk.index', [$desa_id, $rw_id, $kk_id])
            ->with('success', 'Anggota keluarga berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($desa_id, $rw_id, $kk_id, $anggota_id)
    {
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
            'no_kk' => 'nullable|string|max:20',
            'assign_to_kk' => 'nullable|exists:kk,id',
        ]);

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
        ]);

        return redirect()->route('rw.index', [$desa_id, $rw_id])
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroyStandalone($desa_id, $rw_id, $anggota_id)
    {
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
