<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';
    protected $fillable = [
        'judul',
        'konten',
        'tipe_target', //semua, peserta_terpilih
        'status',
        'tgl_terbit',
        'tgl_berakhir',
    ];

    public function penerima()
    {
        return $this->hasMany(PengumumanPeserta::class, 'pengumuman_id');
    }
}
