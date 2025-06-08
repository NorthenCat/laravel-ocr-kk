<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $table = 'desa';

    protected $fillable = [
        'uuid',
        'nama_desa',
        'google_drive',
    ];

    protected $casts = [
        'uuid' => 'string',
        'nama_desa' => 'string',
        'google_drive' => 'string',
    ];

    public function getRw()
    {
        return $this->hasMany(Rw::class, 'desa_id', 'id');
    }

    public function getKK()
    {
        return $this->hasManyThrough(
            KK::class,
            RW::class,
            'desa_id', // Foreign key on RW table
            'rw_id', // Foreign key on KK table
            'id', // Local key on Desa table
            'id' // Local key on RW table
        );
    }

    public function getUsers()
    {
        return $this->hasManyThrough(
            User::class,
            DesaUser::class,
            'desa_id', // Foreign key on DesaUser table
            'id', // Foreign key on User table
            'id', // Local key on Desa table
            'user_id' // Local key on DesaUser table
        );
    }

    public function hasAccess($userId)
    {
        return $this->getUsers()->where('user_id', $userId)->exists();
    }

    public static function getDesaByAccess()
    {
        return self::whereHas('getUsers', function ($query) {
            $query->where('user_id', auth()->id());
        });
    }

    public function getWarga()
    {
        return $this->getKK()->with('getAnggota');
    }
}
