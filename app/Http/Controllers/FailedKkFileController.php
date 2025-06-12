<?php

namespace App\Http\Controllers;

use App\Models\FailedKkFile;
use App\Models\RW;
use Illuminate\Http\Request;

class FailedKkFileController extends Controller
{
    public function show($desa_id, $rw_id, $file_id)
    {
        $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);

        $failedFile = FailedKkFile::where('rw_id', $rw_id)->findOrFail($file_id);

        return view('failed-files.show', compact('rw', 'failedFile'));
    }

    public function markAsProcessed($desa_id, $rw_id, $file_id)
    {
        $failedFile = FailedKkFile::where('rw_id', $rw_id)->findOrFail($file_id);

        $failedFile->update([
            'manually_processed' => true,
            'processed_at' => now(),
        ]);

        return redirect()->route('rw.index', [$desa_id, $rw_id])
            ->with('success', 'File marked as manually processed.');
    }

    public function destroy($desa_id, $rw_id, $file_id)
    {
        $failedFile = FailedKkFile::where('rw_id', $rw_id)->findOrFail($file_id);
        $failedFile->delete();

        return redirect()->route('rw.index', [$desa_id, $rw_id])
            ->with('success', 'Failed file deleted successfully.');
    }
}
