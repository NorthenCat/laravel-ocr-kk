<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $table = 'kk_members';

    protected $fillable = [
        'kk_id',
        'img_name',
        'nama_kepala_keluarga',
        'alamat',
        'rt',
        'rw',
        'kode_pos',
        'desa_kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'no_kk',
        'kk_disahkan_tanggal',
        'nama_lengkap',
        'nik',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'pendidikan',
        'jenis_pekerjaan',
        'golongan_darah',
        'status_perkawinan',
        'status_hubungan_dalam_keluarga',
        'kewarganegaraan',
        'no_paspor',
        'no_kitap',
        'ayah',
        'ibu',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'kk_disahkan_tanggal' => 'date',
    ];

    public function getKk()
    {
        return $this->belongsTo(KK::class, 'kk_id', 'id');
    }

    public function hasKk()
    {
        return !is_null($this->kk_id);
    }

    public function isStandalone()
    {
        return $this->getKk && $this->getKk->no_kk === '0000000000000000';
    }

    public function hasValidKk()
    {
        return $this->hasKk() && $this->getKk && $this->getKk->no_kk !== '0000000000000000';
    }

    public function getDesa()
    {
        return $this->hasOneThrough(
            Desa::class,
            KK::class,
            'id', // Foreign key on KK table
            'id', // Foreign key on Desa table
            'kk_id', // Local key on Anggota table
            'desa_id' // Local key on KK table
        );
    }
    public function getRw()
    {
        return $this->hasOneThrough(
            RW::class,
            KK::class,
            'id', // Foreign key on KK table
            'id', // Foreign key on RW table
            'kk_id', // Local key on Anggota table
            'rw_id' // Local key on KK table
        );
    }
}
