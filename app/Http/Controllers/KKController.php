<?php

namespace App\Http\Controllers;

use App\Models\KK;
use App\Models\RW;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KKController extends Controller
{
    public function index($desa_id, $rw_id, $kk_id)
    {
        $kk = KK::with([
            'getRw.getDesa',
            'getWarga' => function ($query) {
                $query->orderBy('nama_lengkap', 'asc');
            }
        ])->where('rw_id', $rw_id)->findOrFail($kk_id);

        $kk->loadCount('getWarga');

        return view('kk.index', compact('kk'));
    }
    public function create($desa_id, $rw_id)
    {
        $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);
        return view('kk.form', compact('rw'));
    }

    public function store(Request $request, $desa_id, $rw_id)
    {
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


    public function edit($desa_id, $rw_id, $kk_id)
    {
        $kk = KK::with('getRw.getDesa')->where('rw_id', $rw_id)->findOrFail($kk_id);
        return view('kk.form', compact('kk'));
    }

    public function update(Request $request, $desa_id, $rw_id, $kk_id)
    {
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

    public function destroy($desa_id, $rw_id, $kk_id)
    {
        $kk = KK::where('rw_id', $rw_id)->findOrFail($kk_id);
        $kk->delete();

        return redirect()->route('rw.index', [$desa_id, $rw_id])->with('success', 'KK deleted successfully.');
    }

    public function showUpload($desa_id, $rw_id)
    {
        $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);
        return view('kk.upload', compact('rw'));
    }

    public function processUpload(Request $request, $desa_id, $rw_id)
    {
        $request->validate([
            'json_file' => 'required|file|mimes:json|max:10240', // 10MB max
        ]);

        $rw = RW::where('desa_id', $desa_id)->findOrFail($rw_id);

        try {
            $jsonContent = file_get_contents($request->file('json_file')->getRealPath());
            $data = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['json_file' => 'Invalid JSON file format.']);
            }

            // Validate JSON structure
            if (!isset($data['pending']) || !is_array($data['pending'])) {
                return back()->withErrors(['json_file' => 'Invalid JSON structure. Missing "pending" array.']);
            }

            // Check if webhook is configured
            $setting = \App\Models\Setting::first();
            if (!$setting || !$setting->webhook_n8n) {
                return back()->withErrors(['json_file' => 'N8N webhook URL is not configured. Please contact administrator.']);
            }

            $pendingItems = $data['pending'];

            if (empty($pendingItems)) {
                return redirect()->route('rw.index', [$desa_id, $rw_id])->with('success', 'No pending items found in the JSON file.');
            }

            // Create jobs for processing
            $jobs = [];
            foreach ($pendingItems as $item) {
                // Validate required fields
                if (!isset($item['filename']) || !isset($item['raw'])) {
                    continue; // Skip invalid items
                }

                $jobs[] = new \App\Jobs\ProcessKKDataJob($item, $desa_id, $rw_id);
            }

            if (empty($jobs)) {
                return back()->withErrors(['json_file' => 'No valid KK data found in the JSON file.']);
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

                    \Illuminate\Support\Facades\Log::info('All KK data processing jobs completed', [
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

                    \Illuminate\Support\Facades\Log::error('KK data processing batch failed', [
                        'batch_id' => $batch->id,
                        'rw_id' => $rw_id,
                        'error' => $e->getMessage()
                    ]);
                })
                ->finally(function (\Illuminate\Bus\Batch $batch) use ($desa_id, $rw_id) {
                    \Illuminate\Support\Facades\Log::info('KK data processing batch finished', [
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

            return redirect()->route('rw.index', [$desa_id, $rw_id])
                ->with('success', 'JSON file uploaded successfully. Processing ' . count($jobs) . ' items in background. Batch ID: ' . $batch->id);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('JSON upload processing failed', [
                'error' => $e->getMessage(),
                'rw_id' => $rw_id
            ]);

            return back()->withErrors(['json_file' => 'Error processing file: ' . $e->getMessage()]);
        }
    }
}
