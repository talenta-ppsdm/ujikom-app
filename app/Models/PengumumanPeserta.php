<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengumumanPeserta extends Model
{
    protected $table = "pengumuman_peserta";
    protected $fillable = [
        'pengumuman_id',
        'peserta_id',
        'is_read',
    ];

    public function pengumuman()
    {
        return $this->belongsTo(Pengumuman::class, 'pengumuman_id');
    }

    public function peserta()
    {
        return $this->belongsTo(peserta::class, 'peserta_id');
    }
}
