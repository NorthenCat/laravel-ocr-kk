<?php

namespace App\Jobs;

use App\Models\Setting;
use App\Models\KK;
use App\Models\RW;
use App\Models\Anggota;
use App\Models\RwJobStatus;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProcessKKDataJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $kkData;
    protected $rwId;
    protected $desaId;

    public function __construct($kkData, $desaId, $rwId)
    {
        $this->kkData = $kkData;
        $this->rwId = $rwId;
        $this->desaId = $desaId;
    }

    public function handle()
    {
        // Check if batch was cancelled
        if ($this->batch()->cancelled()) {
            return;
        }

        try {
            // Update job status - increment processed jobs
            $this->updateJobProgress();

            // Get webhook URL from settings
            $setting = Setting::first();
            if (!$setting || !$setting->webhook_n8n) {
                throw new \Exception('N8N webhook URL not configured');
            }

            // Prepare data for N8N
            $payload = [
                'filename' => $this->kkData['filename'],
                'raw_text' => $this->kkData['raw'],
                'rw_id' => $this->rwId,
                'desa_id' => $this->desaId,
                'error' => $this->kkData['error'],
                'processed_at' => now()->toISOString()
            ];

            Log::info('Sending KK data to N8N', [
                'filename' => $this->kkData['filename'],
                'rw_id' => $this->rwId,
                'batch_id' => $this->batch()->id,
                'payload_size' => strlen($payload['raw_text'])
            ]);

            // Send to N8N and wait for response (no timeout)
            $response = Http::post($setting->webhook_n8n, $payload);

            if (!$response->successful()) {
                throw new \Exception('N8N request failed with status: ' . $response->status() . ' - ' . $response->body());
            }

            $responseData = $response->json();

            Log::info('Received response from N8N', [
                'filename' => $this->kkData['filename'],
                'response_status' => $response->status(),
                'batch_id' => $this->batch()->id,
                'response_data' => $responseData
            ]);

            // Process the N8N response
            $this->processN8NResponse($responseData);

            Log::info('KK data processed successfully', [
                'filename' => $this->kkData['filename'],
                'rw_id' => $this->rwId,
                'batch_id' => $this->batch()->id
            ]);

        } catch (\Exception $e) {
            // Update job status - increment failed jobs
            $this->updateJobProgress(true);

            Log::error('Failed to process KK data', [
                'filename' => $this->kkData['filename'] ?? 'unknown',
                'error' => $e->getMessage(),
                'rw_id' => $this->rwId,
                'batch_id' => $this->batch()->id ?? 'unknown'
            ]);

            // Re-throw to mark job as failed
            throw $e;
        }
    }

    private function updateJobProgress($failed = false)
    {
        if (!$this->batch()) {
            return;
        }

        try {
            $jobStatus = RwJobStatus::where('batch_id', $this->batch()->id)->first();

            if ($jobStatus) {
                if ($failed) {
                    $jobStatus->increment('failed_jobs');
                } else {
                    $jobStatus->increment('processed_jobs');
                }

                // Check if all jobs are completed
                $totalCompleted = $jobStatus->processed_jobs + $jobStatus->failed_jobs;
                if ($totalCompleted >= $jobStatus->total_jobs) {
                    $jobStatus->update([
                        'status' => $jobStatus->failed_jobs > 0 ? 'completed' : 'completed', // Always completed when finished
                        'completed_at' => now()
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to update job progress', [
                'error' => $e->getMessage(),
                'batch_id' => $this->batch()->id,
                'rw_id' => $this->rwId
            ]);
        }
    }

    private function processN8NResponse(array $responseData)
    {
        // Handle array response - get first item
        $data = is_array($responseData) && isset($responseData[0]) ? $responseData[0] : $responseData;

        Log::info('Processing N8N response data', [
            'filename' => $this->kkData['filename'],
            'isKK' => $data['isKK'] ?? 'not_set',
            'has_anggota' => isset($data['AnggotaKeluarga']),
            'anggota_count' => isset($data['AnggotaKeluarga']) ? count($data['AnggotaKeluarga']) : 0,
            'batch_id' => $this->batch()->id
        ]);

        // Validate response structure
        if (!isset($data['isKK']) || $data['isKK'] !== true) {
            Log::info('N8N response indicates not KK data, skipping', [
                'filename' => $this->kkData['filename'],
                'isKK' => $data['isKK'] ?? null,
                'batch_id' => $this->batch()->id
            ]);
            return;
        }

        // Check for AnggotaKeluarga (capital A)
        if (!isset($data['AnggotaKeluarga']) || empty($data['AnggotaKeluarga'])) {
            Log::warning('No AnggotaKeluarga data in N8N response', [
                'filename' => $this->kkData['filename'],
                'batch_id' => $this->batch()->id,
                'available_keys' => array_keys($data)
            ]);
            return;
        }

        // Validate RW exists
        $rw = RW::where('id', $this->rwId)
            ->where('desa_id', $this->desaId)
            ->first();

        if (!$rw) {
            throw new \Exception('RW not found: ' . $this->rwId);
        }

        DB::beginTransaction();

        try {
            // Extract KK data from first anggota (head of family)
            $kkData = $data['AnggotaKeluarga'][0];

            // Generate NoKK if null
            $noKK = !empty($kkData['NoKK']) ? $kkData['NoKK'] : $this->generateKKNumber($rw);

            // Check if KK already exists
            $existingKK = KK::where('rw_id', $rw->id)->where('no_kk', $noKK)->first();

            if ($existingKK) {
                Log::warning('KK already exists, skipping creation', [
                    'no_kk' => $noKK,
                    'filename' => $this->kkData['filename'],
                    'batch_id' => $this->batch()->id
                ]);
                DB::rollBack();
                return;
            }

            // Create KK
            $kk = KK::create([
                'uuid' => Str::uuid(),
                'no_kk' => $noKK,
                'nama_kepala_keluarga' => $kkData['NamaKepalaKeluarga'] ?? $kkData['NamaLengkap'] ?? 'Unknown',
                'rw_id' => $rw->id,
            ]);

            Log::info('KK created successfully', [
                'kk_id' => $kk->id,
                'no_kk' => $kk->no_kk,
                'nama_kepala_keluarga' => $kk->nama_kepala_keluarga,
                'batch_id' => $this->batch()->id
            ]);

            // Create all anggota keluarga
            $anggotaCount = 0;
            foreach ($data['AnggotaKeluarga'] as $anggotaData) {
                $anggota = $this->createAnggota($kk, $anggotaData);
                $anggotaCount++;

                Log::info('Anggota created', [
                    'anggota_id' => $anggota->id,
                    'nama_lengkap' => $anggota->nama_lengkap,
                    'nik' => $anggota->nik,
                    'kk_id' => $kk->id,
                    'batch_id' => $this->batch()->id
                ]);
            }

            DB::commit();

            Log::info('KK and anggota created from N8N response', [
                'kk_id' => $kk->id,
                'no_kk' => $kk->no_kk,
                'anggota_count' => $anggotaCount,
                'filename' => $this->kkData['filename'],
                'batch_id' => $this->batch()->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save KK data to database', [
                'error' => $e->getMessage(),
                'filename' => $this->kkData['filename'],
                'batch_id' => $this->batch()->id,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function createAnggota(KK $kk, array $anggotaData)
    {
        // Parse tanggal lahir
        $tanggalLahir = null;
        if (!empty($anggotaData['TanggalLahir'])) {
            try {
                $tanggalLahir = Carbon::parse($anggotaData['TanggalLahir']);
            } catch (\Exception $e) {
                Log::warning('Failed to parse tanggal lahir', [
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

    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            return Carbon::parse($dateString)->toDateString();
        } catch (\Exception $e) {
            Log::warning('Failed to parse date', [
                'date_string' => $dateString,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function generateKKNumber(RW $rw)
    {
        $timestamp = now()->format('YmdHis');
        $rwNumber = str_pad($rw->id, 3, '0', STR_PAD_LEFT);

        return $rw->getDesa->id . $rwNumber . $timestamp;
    }

    public function failed(\Throwable $exception)
    {
        // Update job status when job fails permanently
        $this->updateJobProgress(true);

        Log::error('ProcessKKDataJob failed permanently', [
            'filename' => $this->kkData['filename'] ?? 'unknown',
            'error' => $exception->getMessage(),
            'rw_id' => $this->rwId,
            'batch_id' => $this->batch()->id ?? 'unknown'
        ]);
    }
}
