<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemberkasan extends Model
{
    protected $table = 'pemberkasan';
    protected $fillable = [
        'peserta_id',
        'user_id',
        'jadwal_ujian_id',
        'jadwal_pemberkasan_id',
        'nomor_urut',
        'status',
        'catatan',
        'file_sk_golongan',
        'file_sk_jabatan',
        'file_portofolio'
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jadwalUjian()
    {
        return $this->belongsTo(JadwalUjian::class, 'jadwal_ujian_id');
    }

    public function jadwalPemberkasan()
    {
        return $this->belongsTo(JadwalPemberkasan::class, 'jadwal_pemberkasan_id');
    }
}
