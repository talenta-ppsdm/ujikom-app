<?php

namespace App\Models;

use App\Enums\StatusPemberkasanEnum;
use Illuminate\Database\Eloquent\Model;

class Pemberkasan extends Model
{
    protected $table = 'pemberkasan';
    protected $fillable = [
        'peserta_id',
        'jadwal_ujian_id',
        'tujuan_ujian', 
        'tujuan_jenjang',
        'file_sk_golongan',
        'file_sk_jabatan',
        'file_portofolio',
        'file_praasesmen',
        'file_paparan',
        'tanggal_daftar',
        'status',
        'verified_by',
        'verified_at',
        'catatan',
    ];

    protected $casts = [
        'status' => StatusPemberkasanEnum::class,
        'tanggal_daftar' => 'datetime',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function jadwalUjian()
    {
        return $this->belongsTo(JadwalUjian::class, 'jadwal_ujian_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
