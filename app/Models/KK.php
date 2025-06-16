<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KK extends Model
{
    protected $table = 'kk';

    protected $fillable = [
        'uuid',
        'no_kk',
        'rw_id',
        'nama_kepala_keluarga'
    ];

    protected $casts = [
        'uuid' => 'string',
        'nama_kk' => 'string',
        'rw_id' => 'integer',
    ];

    public function getRw()
    {
        return $this->belongsTo(RW::class, 'rw_id', 'id');
    }

    public function getWarga()
    {
        return $this->hasMany(Anggota::class, 'kk_id', 'id');
    }

    public function getFilenameAttribute()
    {
        return $this->getWarga()->first()?->img_name;
    }
}
