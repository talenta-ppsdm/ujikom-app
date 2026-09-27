<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengumumanPeserta extends Model
{
    protected $table = "pengumuman_peserta";
    protected $fillable = [
        'pengumuman_id',
        'peserta_id',
    ];

    public function pengumuman()
    {
        return $this->belongsTo(pengumuman::class, 'pengumuman_id');
    }

    public function peserta()
    {
        return $this->belongsTo(peserta::class, 'peserta_id');
    }
}
