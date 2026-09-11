<?php

namespace App\Models;

use App\Enums\TujuanUjianEnum;
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
        'jabatan_tujuan',
        'jenjang_tujuan'
    ];

    protected $casts = [
        'tanggal_ujian' => 'date',
        'tujuan_ujian' => TujuanUjianEnum::class
    ];

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
