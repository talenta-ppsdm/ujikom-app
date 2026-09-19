<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanUjian extends Model
{
    protected $table = "jawaban_ujian";
    protected $fillable = [
        'jadwal_ujian_id',
        'peserta_id',
        'soal_id',
        'jawaban_terpilih',
        'is_ragu'
    ];

    public function jadwalUjian()
    {
        return $this->belongsTo(JadwalUjian::class, 'jadwal_ujian_id');
    }

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function soal()
    {
        return $this->belongsTo(BankSoal::class, 'soal_id');
    }
}
