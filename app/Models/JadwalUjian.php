<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalUjian extends Model
{
    protected $table = 'jadwal_ujian';
    protected $fillable = [
        'peserta_id',
        'penguji_id',
        'tanggal_ujian',
        'waktu_mulai',
        'waktu_selesai',
        'durasi',
        'lokasi',
        'keterangan',
        'status',
        'tujuan_ujian',
    ];

    protected $casts = [
        'tanggal_ujian' => 'date',
    ];

    public static function getTujuanLabels()
    {
        return [
            'kenaikan jenjang' => 'Ujian Kompetensi Kenaikan Jenjang Jabatan',
            'perpindahan jabatan' => 'Ujian Kompetensi Perpindahan Jabatan',
        ];
    }

    public function getTujuanAttribute(string $value)
    {
        $tujuanLabels = self::getTujuanLabels();
        return $tujuanLabels[$value] ?? $value;
    }

    public static function getStatusLabels()
    {
        return [
            'terjadwal' => 'Terjadwal',
            'sedang berlangsung' => 'Sedang Berlangsung',
            'menunggu hasil' => 'Menunggu Hasil',
        ];
    }

    public function getStatusAttribute(string $value)
    {
        $statusLabels = self::getStatusLabels();
        return $statusLabels[$value] ?? $value;
    }

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function penguji()
    {
        return $this->belongsTo(Penguji::class, 'penguji_id');
    }
}
