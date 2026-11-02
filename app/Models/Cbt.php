<?php

namespace App\Models;

use App\Enums\TujuanUjianEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cbt extends Model
{
    use SoftDeletes;
    
    protected $table = 'cbt';
    protected $fillable = [
        'peserta_id',
        'jadwal_ujian_id',
        'tanggal_ujian',
        'waktu_mulai',
        'waktu_selesai',
        'durasi',
        'keterangan',
        'status',
        'realtime_mulai',
        'realtime_selesai',
        'target_selesai',
        'total_skor'
    ];

    protected $casts = [
        'tanggal_ujian' => 'date',
        'realtime_mulai' => 'datetime',
        'realtime_selesai' => 'datetime',
        'target_selesai' => 'datetime',
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

    public function jadwalUjian()
    {
        return $this->belongsTo(JadwalUjian::class, 'jadwal_ujian_id');
    }

    // public function jawabanUjian()
    // {
    //     return $this->hasMany(JadwalUjian::class, 'jadwal_ujian_id');
    // }
}
