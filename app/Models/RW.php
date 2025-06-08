<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RW extends Model
{
    protected $table = 'rw';

    protected $fillable = [
        'uuid',
        'nama_rw',
        'desa_id',
        'google_drive'
    ];

    protected $casts = [
        'uuid' => 'string',
        'nama_rw' => 'string',
        'desa_id' => 'integer',
    ];

    public function getDesa()
    {
        return $this->belongsTo(Desa::class, 'desa_id', 'id');
    }

    public function getKK()
    {
        return $this->hasMany(KK::class, 'rw_id', 'id');
    }

    public function getWarga()
    {
        return $this->hasManyThrough(
            Anggota::class,
            KK::class,
            'rw_id', // Foreign key on KK table
            'kk_id', // Foreign key on Anggota table
            'id', // Local key on RW table
            'id' // Local key on KK table
        );
    }

    public function getJobStatus()
    {
        return $this->hasMany(\App\Models\RwJobStatus::class, 'rw_id', 'id');
    }

    public function getCurrentJobStatus()
    {
        return $this->hasOne(\App\Models\RwJobStatus::class, 'rw_id', 'id')
            ->where('status', 'processing')
            ->latest();
    }

    public function hasActiveJob()
    {
        return $this->getCurrentJobStatus()->exists();
    }
}
