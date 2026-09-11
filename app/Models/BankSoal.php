<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankSoal extends Model
{
    protected $table = 'banksoal';
    protected $fillable = [
        'kode',
        'kategori',
        'level',
        'soal',
        'jawaban_a',
        'jawaban_b',
        'jawaban_c',
        'jawaban_d',
        'jawaban_e',
        'kunci',
        'pembahasan'
    ];

    public static function getLevelLabels()
    {
        return [
            1 => 'Level 1 - JF Ahli Pertama',
            2 => 'Level 2 - JF Ahli Muda',
            3 => 'Level 3 - JF Ahli Muda',
            4 => 'Level 4 - JF Ahli Madya',
            5 => 'Level 5 - JF Ahli Utama',
        ];
    }

    public function getLevelNameAttribute()
    {
        $levelLabels = self::getLevelLabels();
        return $levelLabels[$this->level] ?? 'Level tidak diketahui';
    }

    public static function getcategoryLabels()
    {
        return [
            'mata rantai perumahan' => 'Mata Rantai Perumahan',
            'pengembangan kawasan permukiman' => 'Pengembangan Kawasan Permukiman',
            'teknis konstruksi & standar rumah layak huni' => 'Teknis Konstruksi & Standar Rumah Layak Huni',
            'tata kelola & kelembagaan perumahan' => 'Tata Kelola & Kelembagaan Perumahan',
        ];
    }

    public function getCategoryNamaAttribute()
    {
        $categoryLabels = self::getcategoryLabels();
        return $categoryLabels[$this->kategori] ?? 'Kategori tidak diketahui';
    }
}
