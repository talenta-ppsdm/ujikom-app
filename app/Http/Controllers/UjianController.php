<?php

namespace App\Http\Controllers;

use App\Enums\StatusJadwalUjianEnum;
use App\Repositories\BankSoalRepository;
use App\Repositories\JadwalUjianRepository;
use Illuminate\Http\Request;

class UjianController extends Controller
{
    protected JadwalUjianRepository $jadwalUjianRepository;
    protected BankSoalRepository $bankSoalRepository;

    public function __construct(
        JadwalUjianRepository $jadwalUjianRepository,
        BankSoalRepository $bankSoalRepository
    )
    {
        $this->jadwalUjianRepository = $jadwalUjianRepository;
        $this->bankSoalRepository = $bankSoalRepository;
    }

    public function start(int $id)
    {
        $jadwalUjian = $this->jadwalUjianRepository->find($id, 'id')->first();

        // HANDLING EXAM TIME
        // Check first time participant trigger button start
        if (!$jadwalUjian->realtime_mulai) {
            $waktuMulai = now();
            $durasiMenit = (int) $jadwalUjian->durasi;

            $jadwalUjian->update([
                'realtime_mulai' => $waktuMulai,
                'target_selesai'   => $waktuMulai->copy()->addMinutes($durasiMenit), //Carbon mutable (changing the original variable's value if not copied)
                'status'           => StatusJadwalUjianEnum::SEDANG_BERLANGSUNG->value,
            ]);
        }

        // check if the exam time is over (for example, the user closes the tab and then opens it again when the time runs out)
        if (now()->greaterThanOrEqualTo($jadwalUjian->target_selesai)) {
            if ($jadwalUjian->status !== StatusJadwalUjianEnum::MENUNGGU_HASIL->value) {
                $jadwalUjian->update([
                    'realtime_selesai' => $jadwalUjian->target_selesai,
                    'status'           => StatusJadwalUjianEnum::MENUNGGU_HASIL->value,
                ]);
            }
            return redirect('/beranda')->with('error', 'Waktu ujian telah habis.');
        }

        // Change target_selesai to unix format
        $targetTimestamp = $jadwalUjian->target_selesai->timestamp;


        // HANDLING EXAM QUESTION
        $totalSoal = 30;

        $levelQuest = [
            'ahli pertama'  => [1],
            'ahli muda'     => [2,3],
            'ahli madya'    => [4],
            'ahli utama'    => [5],
        ];
        $levelPeserta = $levelQuest[strtolower($jadwalUjian->jenjang_tujuan)];

        $listKategoriSoal = $this->bankSoalRepository->getCategoryByLevel($levelPeserta);
        $jumlahKategori = $listKategoriSoal->count();

        if ($jumlahKategori === 0) {
            return redirect('/beranda')->with('error', 'Belum ada soal tersedia untuk jenajng tujuan anda');
        }

        $limitPerKategori = (int) floor($totalSoal / $jumlahKategori);
        $sisaSoal = $totalSoal % $jumlahKategori;

        $soalUjian = collect();
        foreach ($listKategoriSoal as $index => $kategori) {
            // If there's a remainder (for example 30/7 = 4 remainder 2), the first 2 categories can get 1 extra question
            $limit = $limitPerKategori + ($index < $sisaSoal ? 1 : 0);
    
            $soal = $this->bankSoalRepository->getByLevalAndCategory($levelPeserta, $kategori, $limit);
    
            $soalUjian = $soalUjian->merge($soal);
        }
    
        // Shuffle question
        $soalUjian = $soalUjian->shuffle();

        return view('ujian.show', compact('jadwalUjian', 'targetTimestamp', 'soalUjian'));
    }
}
