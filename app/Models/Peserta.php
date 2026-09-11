<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    protected $table = 'peserta';
    protected $fillable =[
        'nama',
        'nip',
        'golongan',
        'jabatan',
        'unit',
        'instansi',
        'telepon',
        'email',
        'user_id',
    ];

    public function jadwalUjian()
    {
        return $this->hasMany(JadwalUjian::class, 'peserta_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
