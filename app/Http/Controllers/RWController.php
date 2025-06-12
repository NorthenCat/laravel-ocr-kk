<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\RW;
use App\Models\Desa;
use App\Exports\AnggotaExport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class RWController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($desa_id, $rw_id)
    {

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
            ->pluck('RT')
            ->map(function ($rt) {
                return (int) $rt;
            })
            ->unique()
            ->count();




        return view('rw.index', compact('rw', 'standaloneAnggota', 'anggotaTanpaNik', 'failedFiles', 'totalRT'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($desa_id)
    {
        $desa = Desa::findOrFail($desa_id);
        return view('rw.form', compact('desa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $desa_id)
    {
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($desa_id, $rw_id)
    {
        $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);
        return view('rw.form', compact('rw'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $desa_id, $rw_id)
    {
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($desa_id, $rw_id)
    {
        $rw = RW::where('desa_id', $desa_id)->findOrFail($rw_id);
        $rw->delete();

        return redirect()->route('desa.show', $desa_id)->with('success', 'RW deleted successfully.');
    }

    /**
     * Export to Excel.
     */
    public function exportExcel($desa_id, $rw_id)
    {
        $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);

        $fileName = 'KK-OCR_' . $rw->getDesa->nama_desa . '_' . $rw->nama_rw . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new AnggotaExport($rw_id), $fileName);
    }
}
