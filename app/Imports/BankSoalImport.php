<?php

namespace App\Imports;

use App\Models\BankSoal;
use App\Repositories\BankSoalRepository;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BankSoalImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    private string $lastKode;
    protected BankSoalRepository $bankSoalRepository;

    public function __construct(BankSoalRepository $bankSoalRepository)
    {
        $this->lastKode = $bankSoalRepository->getLastKode();
    }

    public function model(array $row)
    {
        if (!empty($row['kode'])) {
            $newCode = $row['kode'];
        } else {
            if ($this->lastKode !== null) {
                $numberLastCode = (int) substr($this->lastKode, 2);
            } else {
                $numberLastCode = 0;
            }
    
            $nextNumberCode = $numberLastCode + 1;
            $newCode = 'S-' . sprintf('%04d', $nextNumberCode);
    
            $this->lastKode = $newCode;
        }
      
        return new BankSoal([
            'soal'          => $row['soal'], 
            'jawaban_a'     => $row['jawaban_a'],
            'jawaban_b'     => $row['jawaban_b'],
            'jawaban_c'     => $row['jawaban_c'],
            'jawaban_d'     => $row['jawaban_d'],
            'jawaban_e'     => $row['jawaban_e'],
            'kunci'         => $row['kunci'],
            'level'         => $row['level'],
            'kategori'      => $row['kategori'],
            'poin'          => $row['poin'],
            'pembahasan'    => $row['pembahasan']??'-',
            'kode'          => $newCode
        ]);
    }
}
