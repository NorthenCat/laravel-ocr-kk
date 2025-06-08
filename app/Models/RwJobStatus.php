<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RwJobStatus extends Model
{
    protected $table = 'rw_job_status';

    protected $fillable = [
        'rw_id',
        'batch_id',
        'status',
        'total_jobs',
        'processed_jobs',
        'failed_jobs',
        'error_message',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function getRw()
    {
        return $this->belongsTo(RW::class, 'rw_id', 'id');
    }

    public function getProgressPercentage()
    {
        if ($this->total_jobs == 0) return 0;
        return round(($this->processed_jobs / $this->total_jobs) * 100, 1);
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function hasFailed()
    {
        return $this->status === 'failed';
    }
}
