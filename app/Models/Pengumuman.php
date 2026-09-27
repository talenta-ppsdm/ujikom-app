<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengumuman extends Model
{
    use SoftDeletes;
    
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
