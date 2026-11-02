<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JadwalUjian extends Model
{
    use SoftDeletes;

    protected $table = 'jadwal_ujian';
    protected $fillable = [
        'gelombang',
        'tahun',
        'status',
        'kuota',
        'periode_awal',
        'periode_akhir',
        'periode_pemberkasan_awal',
        'periode_pemberkasan_akhir',
        'periode_portofolio_awal',
        'periode_portofolio_akhir',
    ];

    protected $casts = [
        'periode_awal'              => 'date',
        'periode_akhir'             => 'date',
        'periode_pemberkasan_awal'  => 'date',
        'periode_pemberkasan_akhir' => 'date',
        'periode_portofolio_awal'   => 'date',
        'periode_portofolio_akhir'  => 'date',
    ];
}
