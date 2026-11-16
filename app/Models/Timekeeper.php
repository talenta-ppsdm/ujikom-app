<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timekeeper extends Model
{
    protected $table = 'admin';

    protected $fillable = [
        'nip',
        'nama',
        'unit',
        'instansi',
        'telepon',
        'email',
        'user_id',
        'foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
