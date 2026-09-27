<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peserta extends Model
{
    use SoftDeletes;
    
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
