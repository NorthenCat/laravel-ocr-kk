<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FailedKkFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'rw_id',
        'batch_id',
        'filename',
        'original_filename',
        'file_path',
        'raw_text',
        'failure_reason',
        'error_message',
        'n8n_response',
        'manually_processed'
    ];

    protected $casts = [
        'n8n_response' => 'array',
        'manually_processed' => 'boolean'
    ];

    public function rw()
    {
        return $this->belongsTo(RW::class);
    }

    public function getFailureReasonTextAttribute()
    {
        return match ($this->failure_reason) {
            'not_kk' => 'Not KK Data',
            'processing_error' => 'Processing Error',
            'no_anggota_data' => 'No Member Data',
            default => 'Unknown Error'
        };
    }

    // Delete associated image file when model is deleted
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($failedFile) {
            if ($failedFile->file_path && Storage::disk('local')->exists($failedFile->file_path)) {
                Storage::disk('local')->delete($failedFile->file_path);
            }
        });
    }
}
