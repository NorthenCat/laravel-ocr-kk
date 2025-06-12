<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedKkFile extends Model
{
    protected $fillable = [
        'rw_id',
        'batch_id',
        'filename',
        'raw_text',
        'failure_reason',
        'error_message',
        'n8n_response',
        'manually_processed',
        'processed_at',
    ];

    protected $casts = [
        'n8n_response' => 'array',
        'manually_processed' => 'boolean',
        'processed_at' => 'datetime',
    ];

    public function getRw()
    {
        return $this->belongsTo(RW::class, 'rw_id');
    }

    public function getFailureReasonTextAttribute()
    {
        return match ($this->failure_reason) {
            'not_kk' => 'Bukan data KK',
            'processing_error' => 'Error pemrosesan',
            'no_anggota_data' => 'Tidak ada data anggota',
            default => 'Unknown'
        };
    }
}
