<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesaUser extends Model
{
    protected $table = 'desa_users';

    protected $fillable = [
        'user_id',
        'desa_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'desa_id' => 'integer',
    ];

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getDesa()
    {
        return $this->belongsTo(Desa::class, 'desa_id', 'id');
    }
}
