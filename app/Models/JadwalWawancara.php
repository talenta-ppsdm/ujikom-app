<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalWawancara extends Model
{
    protected $table = 'jadwal_wawancara';
    protected $fillable = [
        'peserta_id',
        'penguji_id',
        'jadwal_ujian_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'metode',
        'lokasi_link',
        'status'
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function penguji()
    {
        return $this->belongsTo(Penguji::class, 'penguji_id');
    }

    public function jadwalUjian()
    {
        return $this->belongsTo(JadwalUjian::class, 'jadwal_ujian_id');
    }
}
