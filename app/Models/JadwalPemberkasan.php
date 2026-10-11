<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPemberkasan extends Model
{
    protected $table = 'jadwal_pemberkasan';
    protected $fillable = [
        'gelombang',
        'tahun',
        'status',
        'kuota',
        'periode_awal',
        'periode_akhir',
    ];

    protected $casts = [
        'periode_awal'  => 'date',
        'periode_akhir' => 'date',
    ];
}
