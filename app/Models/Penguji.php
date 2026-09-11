<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penguji extends Model
{
    protected $table = 'penguji';
    protected $fillable =[
        'nama',
        'nip',
        'golongan',
        'jabatan',
        'unit',
        'instansi',
        'telepon',
        'email',
        'bidang_keahlian',
        'user_id',
    ];

    public function jadwalUjian()
    {
        return $this->hasMany(JadwalUjian::class, 'penguji_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
