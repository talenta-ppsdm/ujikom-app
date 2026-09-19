<?php

namespace App\Http\Controllers;

use App\Enums\StatusJadwalUjianEnum;
use App\Repositories\BankSoalRepository;
use App\Repositories\JadwalUjianRepository;
use App\Repositories\JawabanUjianRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UjianController extends Controller
{
    protected JadwalUjianRepository $jadwalUjianRepository;
    protected BankSoalRepository $bankSoalRepository;
    protected JawabanUjianRepository $jawabanUjianRepository;

    public function __construct(
        JadwalUjianRepository $jadwalUjianRepository,
        BankSoalRepository $bankSoalRepository,
        JawabanUjianRepository $jawabanUjianRepository
    )
    {
        $this->jadwalUjianRepository = $jadwalUjianRepository;
        $this->bankSoalRepository = $bankSoalRepository;
        $this->jawabanUjianRepository = $jawabanUjianRepository;
    }

    public function start(int $jadwalUjianId)
    {
        $jadwalUjian = $this->jadwalUjianRepository->find($jadwalUjianId, 'id')->first();
        $pesertaId = Auth::id();

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
        if ($jadwalUjian->target_selesai && now()->greaterThanOrEqualTo($jadwalUjian->target_selesai)) {
            if ($jadwalUjian->getRawOriginal('status') !== StatusJadwalUjianEnum::MENUNGGU_HASIL->value) {
                $jadwalUjian->update([
                    'realtime_selesai' => $jadwalUjian->target_selesai,
                    'status'           => StatusJadwalUjianEnum::MENUNGGU_HASIL->value,
                ]);
            }
            return redirect('/beranda')->with('error', 'Waktu ujian telah habis.');
        }

        // Change target_selesai to unix format
        $targetTimestamp = $jadwalUjian->target_selesai->timestamp;

        // HANDLING EXAM QUESTION AND EXISTING ANSWERS
        $jawabanTerpilih = $this->jawabanUjianRepository->getByPesertaAndJadwal($pesertaId, $jadwalUjianId);
     
        if ($jawabanTerpilih->isNotEmpty()) {
            $soalIds = $jawabanTerpilih->pluck('soal_id');
            $soalUjian = $this->bankSoalRepository->getByIds($soalIds);
            $keyJawabanTerpilih = $jawabanTerpilih->keyBy('soal_id'); // changing index in array to the question id
        }else{
            $totalSoal = 30;
            $levelQuest = [
                'ahli pertama'  => [1],
                'ahli muda'     => [2,3],
                'ahli madya'    => [4],
                'ahli utama'    => [5],
            ];
            $levelPeserta = $levelQuest[strtolower($jadwalUjian->jenjang_tujuan)] ?? [];
    
            $listKategoriSoal = $this->bankSoalRepository->getCategoryByLevel($levelPeserta);
            $jumlahKategori = $listKategoriSoal->count();
    
            $soalUjian = collect();
            if ($jumlahKategori > 0) {
                $limitPerKategori = (int) floor($totalSoal / $jumlahKategori);
                $sisaSoal = $totalSoal % $jumlahKategori;
    
                foreach ($listKategoriSoal as $index => $kategori) {
                    // If there's a remainder (for example 30/7 = 4 remainder 2), the first 2 categories can get 1 extra question
                    $limit = $limitPerKategori + ($index < $sisaSoal ? 1 : 0);
    
                    $soal = $this->bankSoalRepository->getByLevalAndCategory($levelPeserta, $kategori, $limit);
    
                    $soalUjian = $soalUjian->merge($soal);
                }
            }
        
            // Shuffle question
            $soalUjian = $soalUjian->shuffle();

            foreach ($soalUjian as $soal) {
                $this->jawabanUjianRepository->create([
                    'jadwal_ujian_id'   => $jadwalUjianId,
                    'peserta_id'        => $pesertaId,
                    'soal_id'           => $soal->id,
                    'jawaban_terpilih'  => null,
                    'is_ragu'           =>false,
                ]);
            }

            $keyJawabanTerpilih = collect();
        }
 
        return view('peserta.ujian', compact('jadwalUjian', 'targetTimestamp', 'soalUjian', 'keyJawabanTerpilih'));
    }

    public function saveAnswer(Request $request)
    {
        $request->validate([
            'jadwal_ujian_id' => 'required|exists:jadwal_ujian,id',
            'soal_id'         => 'required|exists:banksoal,id',
            'jawaban'         => 'nullable|in:a,b,c,d,e',
            'is_ragu'         => 'boolean',
        ]);

        $jawaban = $this->jawabanUjianRepository->updateOrCreate([
            'jadwal_ujian_id'   => $request->jadwal_ujian_id,
            'peserta_id'        => Auth::id(),
            'soal_id'           => $request->soal_id
        ],
        [
            'jawaban_terpilih'  => $request->jawaban,
            'is_ragu'           => $request->is_ragu ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jawaban berhasil disimpan',
            'data' => $jawaban
        ], 200);
    }
}
