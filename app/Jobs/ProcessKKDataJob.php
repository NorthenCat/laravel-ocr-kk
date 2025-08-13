<?php

namespace App\Jobs;

use App\Models\Setting;
use App\Models\KK;
use App\Models\RW;
use App\Models\Anggota;
use App\Models\RwJobStatus;
use App\Models\FailedKkFile;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

            $filePath = $this->kkData['file_path'];

            // Check if file exists
            if (!Storage::disk('local')->exists($filePath)) {
                throw new \Exception('Image file not found: ' . $filePath);
            }

            // Get the full system path to the file
            $fullFilePath = Storage::disk('local')->path($filePath);

            Log::info('Sending KK image to N8N for OCR', [
                'filename' => $this->kkData['filename'],
                'rw_id' => $this->rwId,
                'batch_id' => $this->batch()->id,
                'file_path' => $filePath
            ]);

            // Send to N8N as multipart form data with the actual file
            $response = Http::timeout(300)
                ->attach('image', file_get_contents($fullFilePath), $this->kkData['filename'])
                ->post($setting->webhook_n8n, [
                    'filename' => $this->kkData['filename'],
                    'original_filename' => $this->kkData['original_filename'] ?? $this->kkData['filename'],
                    'rw_id' => $this->rwId,
                    'desa_id' => $this->desaId,
                    'processed_at' => now()->toISOString()
                ]);

            if (!$response->successful()) {
                throw new \Exception('N8N request failed with status: ' . $response->status() . ' - ' . $response->body());
            }

            $responseData = $response->json();

            Log::info('Received OCR response from N8N', [
                'filename' => $this->kkData['filename'],
                'response_status' => $response->status(),
                'batch_id' => $this->batch()->id,
                'has_response_data' => !empty($responseData)
            ]);

            // Delete the image file after successful processing)
            Storage::disk('local')->delete($filePath);

            // Process the N8N response
            $this->processN8NResponse($responseData);



            Log::info('KK image processed successfully and file deleted', [
                'filename' => $this->kkData['filename'],
                'rw_id' => $this->rwId,
                'batch_id' => $this->batch()->id
            ]);

        } catch (\Exception $e) {
            // Update job status - increment failed jobs
            $this->updateJobProgress(true);

            Log::error('Failed to process KK image', [
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
                        'status' => 'completed',
                        'completed_at' => now()
                    ]);
                } else {
                    // Update status to running if still processing
                    $jobStatus->update(['status' => 'running']);
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

        Log::info('Processing N8N OCR response data', [
            'filename' => $this->kkData['filename'],
            'isKK' => $data['isKK'] ?? 'not_set',
            'has_anggota' => isset($data['AnggotaKeluarga']),
            'anggota_count' => isset($data['AnggotaKeluarga']) ? count($data['AnggotaKeluarga']) : 0,
            'batch_id' => $this->batch()->id
        ]);

        // Validate response structure
        if (!isset($data['isKK']) || $data['isKK'] !== true) {
            // Save as failed file for manual processing
            FailedKkFile::create([
                'rw_id' => $this->rwId,
                'batch_id' => $this->batch()->id,
                'filename' => $this->kkData['filename'],
                'original_filename' => $this->kkData['original_filename'] ?? null,
                'file_path' => $this->kkData['file_path'],
                'raw_text' => $data['raw_text'] ?? null,
                'failure_reason' => 'not_kk',
                'error_message' => 'N8N response indicates this is not KK data',
                'n8n_response' => $data,
            ]);

            Log::info('N8N response indicates not KK data, saved for manual processing', [
                'filename' => $this->kkData['filename'],
                'isKK' => $data['isKK'] ?? null,
                'batch_id' => $this->batch()->id
            ]);
            return;
        }

        // Check for AnggotaKeluarga
        if (!isset($data['AnggotaKeluarga']) || empty($data['AnggotaKeluarga'])) {
            // Save as failed file for manual processing
            FailedKkFile::create([
                'rw_id' => $this->rwId,
                'batch_id' => $this->batch()->id,
                'filename' => $this->kkData['filename'],
                'original_filename' => $this->kkData['original_filename'] ?? null,
                'file_path' => $this->kkData['file_path'],
                'raw_text' => $data['raw_text'] ?? null,
                'failure_reason' => 'no_anggota_data',
                'error_message' => 'No AnggotaKeluarga data found in N8N response',
                'n8n_response' => $data,
            ]);

            Log::warning('No AnggotaKeluarga data in N8N response, saved for manual processing', [
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

            // Check if NoKK exists - if null, create KK with 16 zeros
            if (empty($kkData['NoKK'])) {
                Log::info('NoKK is null/empty, creating KK with 16 zeros for standalone anggota', [
                    'filename' => $this->kkData['filename'],
                    'anggota_count' => count($data['AnggotaKeluarga']),
                    'batch_id' => $this->batch()->id
                ]);

                $noKK = '0000000000000000'; // 16 zeros

                // Check if KK with 16 zeros already exists in this RW
                $existingKK = KK::where('rw_id', $rw->id)->where('no_kk', $noKK)->first();

                if (!$existingKK) {
                    // Create KK with 16 zeros
                    $kk = KK::create([
                        'uuid' => Str::uuid(),
                        'no_kk' => $noKK,
                        'nama_kepala_keluarga' => 'Anggota Tanpa KK',
                        'rw_id' => $rw->id,
                    ]);

                    Log::info('KK with 16 zeros created', [
                        'kk_id' => $kk->id,
                        'no_kk' => $kk->no_kk,
                        'batch_id' => $this->batch()->id
                    ]);
                } else {
                    $kk = $existingKK;
                    Log::info('Using existing KK with 16 zeros', [
                        'kk_id' => $kk->id,
                        'no_kk' => $kk->no_kk,
                        'batch_id' => $this->batch()->id
                    ]);
                }

                // Create all anggota and assign to the zero KK
                $anggotaCount = 0;
                foreach ($data['AnggotaKeluarga'] as $anggotaData) {
                    $anggota = $this->createAnggota($kk, $anggotaData);
                    $anggotaCount++;

                    Log::info('Anggota created and assigned to zero KK', [
                        'anggota_id' => $anggota->id,
                        'nama_lengkap' => $anggota->nama_lengkap,
                        'nik' => $anggota->nik,
                        'kk_id' => $kk->id,
                        'filename' => $this->kkData['filename'],
                        'batch_id' => $this->batch()->id
                    ]);
                }

                DB::commit();

                Log::info('Anggota records created and assigned to zero KK', [
                    'kk_id' => $kk->id,
                    'no_kk' => $kk->no_kk,
                    'anggota_count' => $anggotaCount,
                    'filename' => $this->kkData['filename'],
                    'batch_id' => $this->batch()->id
                ]);

                return;
            }

            $noKK = $kkData['NoKK'];

            // Check if KK already exists
            $existingKK = KK::where('rw_id', $rw->id)->where('no_kk', $noKK)->first();

            if ($existingKK) {
                Log::warning('KK already exists, skipping creation', [
                    'no_kk' => $noKK,
                    'filename' => $this->kkData['filename'],
                    'batch_id' => $this->batch()->id
                ]);
                DB::rollBack();
                //create failed jobs
                FailedKkFile::create([
                    'rw_id' => $this->rwId,
                    'batch_id' => $this->batch()->id,
                    'filename' => $this->kkData['filename'],
                    'original_filename' => $this->kkData['original_filename'] ?? null,
                    'file_path' => $this->kkData['file_path'],
                    'raw_text' => $data['raw_text'] ?? null,
                    'failure_reason' => 'processing_error',
                    'error_message' => 'KK already exists for this RW',
                    'n8n_response' => $data,
                ]);
                DB::commit();
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

    private function createStandaloneAnggota(array $anggotaData)
    {
        // Parse tanggal lahir
        $tanggalLahir = null;
        if (!empty($anggotaData['TanggalLahir'])) {
            try {
                $tanggalLahir = Carbon::parse($anggotaData['TanggalLahir']);
            } catch (\Exception $e) {
                Log::warning('Failed to parse tanggal lahir for standalone anggota', [
                    'tanggal_lahir' => $anggotaData['TanggalLahir'],
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Create anggota without KK association
        $anggota = Anggota::create([
            'kk_id' => null, // No KK association
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
            'no_kk' => null, // No KK number
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

        // Save as failed file for manual processing (keep the image file)
        FailedKkFile::create([
            'rw_id' => $this->rwId,
            'batch_id' => $this->batch()->id ?? 'unknown',
            'filename' => $this->kkData['filename'] ?? 'unknown',
            'original_filename' => $this->kkData['original_filename'] ?? null,
            'file_path' => $this->kkData['file_path'] ?? null,
            'raw_text' => null,
            'failure_reason' => 'processing_error',
            'error_message' => $exception->getMessage(),
        ]);

        Log::error('ProcessKKDataJob failed permanently, saved for manual processing', [
            'filename' => $this->kkData['filename'] ?? 'unknown',
            'error' => $exception->getMessage(),
            'rw_id' => $this->rwId,
            'batch_id' => $this->batch()->id ?? 'unknown'
        ]);
    }
}
