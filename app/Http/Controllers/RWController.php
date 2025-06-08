<?php

namespace App\Http\Controllers;

use App\Models\RW;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RWController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($desa_id, $rw_id)
    {

        $rw = RW::with([
            'getDesa',
            'getKK.getAnggota',
            'getKK' => function ($query) {
                $query->orderBy('no_kk', 'asc');
            },
            'getCurrentJobStatus',
            'getJobStatus' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(5);
            }
        ])->where('desa_id', $desa_id)->findOrFail($rw_id);

        $rw->loadCount(['getKK', 'getWarga']);

        return view('rw.index', compact('rw'));
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
}
