<?php

namespace App\Http\Controllers;

use App\Enums\StatusJadwalCbtEnum;
use App\Repositories\BankSoalRepository;
use App\Repositories\CbtRepository;
use App\Repositories\PemberkasanRepository;
use App\Repositories\JawabanUjianRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UjianController extends Controller
{
    protected CbtRepository $cbtRepository;
    protected BankSoalRepository $bankSoalRepository;
    protected JawabanUjianRepository $jawabanUjianRepository;
    protected PemberkasanRepository $pemberkasanRepository;

    public function __construct(
        CbtRepository $cbtRepository,
        BankSoalRepository $bankSoalRepository,
        JawabanUjianRepository $jawabanUjianRepository,
        PemberkasanRepository $pemberkasanRepository
    )
    {
        $this->cbtRepository = $cbtRepository;
        $this->bankSoalRepository = $bankSoalRepository;
        $this->jawabanUjianRepository = $jawabanUjianRepository;
        $this->pemberkasanRepository = $pemberkasanRepository;
    }

    public function start(int $jadwalCbtId)
    {
        $jadwalCbt = $this->cbtRepository->find($jadwalCbtId);
        $pesertaId = Auth::user()->peserta->id;
        $jadwalUjianId = $jadwalCbt->jadwal_ujian_id;

        // HANDLING EXAM TIME
        $waktuMulaiUjian = Carbon::parse($jadwalCbt->tanggal_ujian->format('Y-m-d') . ' ' . $jadwalCbt->waktu_mulai);
        $waktuSelesaiUjian = Carbon::parse($jadwalCbt->tanggal_ujian->format('Y-m-d') . ' ' . $jadwalCbt->waktu_selesai);
        $now = Carbon::now();

        if ($now->lt($waktuMulaiUjian)) {
            return redirect()->route('peserta.dashboard-peserta.index')->with('error', 'Ujian belum dimulai.');
        } 
        elseif ($now->gt($waktuSelesaiUjian)) {
            if ($jadwalCbt->getRawOriginal('status') !== StatusJadwalCbtEnum::SELESAI->value) {
                $jumlahJawaban = $this->jawabanUjianRepository->getByPesertaAndJadwalUjian($pesertaId, $jadwalUjianId)->count();

                if ($jumlahJawaban > 0) {
                    $this->calculateAndCompleteExam($pesertaId, $jadwalUjianId);
                }

                $jadwalCbt->update([
                    'realtime_selesai' => $jadwalCbt->target_selesai,
                    'status'           => StatusJadwalCbtEnum::SELESAI->value,
                ]);
            }
            return redirect()->route('peserta.dashboard-peserta.index')->with('error', 'Waktu ujian telah habis.');
        } else {
            if (!$jadwalCbt->realtime_mulai) {
                $waktuMulai = now();
                $durasiMenit = (int) $jadwalCbt->durasi;
            
                $jadwalCbt->update([
                    'realtime_mulai' => $waktuMulai,
                    'target_selesai' => $waktuMulai->copy()->addMinutes($durasiMenit),
                    'status'         => StatusJadwalCbtEnum::SEDANG_BERLANGSUNG->value,
                ]);

                $jadwalCbt->refresh();
            }

            if ($jadwalCbt->target_selesai && now()->greaterThanOrEqualTo($jadwalCbt->target_selesai)) {
                if ($jadwalCbt->getRawOriginal('status') !== StatusJadwalCbtEnum::SELESAI->value) {
                    $jumlahJawaban = $this->jawabanUjianRepository->getByPesertaAndJadwalUjian($pesertaId, $jadwalUjianId)->count();
                    
                    if ($jumlahJawaban > 0) {
                        $this->calculateAndCompleteExam($pesertaId, $jadwalUjianId);
                    }

                    $jadwalCbt->update([
                        'realtime_selesai' => $jadwalCbt->target_selesai,
                        'status'           => StatusJadwalCbtEnum::SELESAI->value,
                    ]);
                }
                return redirect()->route('peserta.dashboard-peserta.index')->with('error', 'Waktu ujian telah habis.');
            }

            $targetTimestamp = $jadwalCbt->target_selesai ? $jadwalCbt->target_selesai->timestamp : 0;

            $jawabanTerpilih = collect();
            $keyJawabanTerpilih = collect();
            $soalUjian = collect();
            
            $jawabanTerpilih = $this->jawabanUjianRepository->getByPesertaAndJadwalCbt($pesertaId, $jadwalCbtId);
            
            if ($jawabanTerpilih->isNotEmpty()) {
                $soalIds = $jawabanTerpilih->pluck('soal_id');
                $soalUjian = $this->bankSoalRepository->getByIds($soalIds);
                
                // Urutkan kembali sesuai urutan awal saat di-shuffle pertama kali
                $soalUjian = $soalUjian->sortBy(function($model) use ($soalIds) {
                    return $soalIds->search($model->id);
                })->values();
    
                $keyJawabanTerpilih = $jawabanTerpilih->keyBy('soal_id'); 
            } else {
                $existingJawaban = $this->jawabanUjianRepository->findWhere([
                    'jadwal_ujian_id' => $jadwalCbt->jadwal_ujian_id,
                    'peserta_id'      => $pesertaId,
                ]);
                
                if ($existingJawaban->isNotEmpty()) {
                    $soalIds = $existingJawaban->pluck('soal_id');
                    $soalUjian = $this->bankSoalRepository->getByIds($soalIds);
                    
                    // Urutkan kembali agar posisinya stabil
                    $soalUjian = $soalUjian->sortBy(function($model) use ($soalIds) {
                        return $soalIds->search($model->id);
                    })->values();
    
                    $keyJawabanTerpilih = $existingJawaban->keyBy('soal_id');
                } else {
                    $totalSoal = 30;
                    $levelQuest = [
                        'ahli pertama'  => [1],
                        'ahli muda'     => [2,3],
                        'ahli madya'    => [4],
                        'ahli utama'    => [5],
                    ];
                
                    $pemberkasan = $this->pemberkasanRepository->getByPesertaAndSchedule($pesertaId, $jadwalCbt->jadwal_ujian_id)->first();
                    $levelPeserta = $levelQuest[strtolower($pemberkasan->tujuan_jenjang)] ?? [];
                
                    $listKategoriSoal = $this->bankSoalRepository->getCategoryByLevel($levelPeserta);
                    $jumlahKategori = $listKategoriSoal->count();
                
                    if ($jumlahKategori > 0) {
                        $limitPerKategori = (int) floor($totalSoal / $jumlahKategori);
                        $sisaSoal = $totalSoal % $jumlahKategori;
                
                        foreach ($listKategoriSoal as $index => $kategori) {
                            $limit = $limitPerKategori + ($index < $sisaSoal ? 1 : 0);
                            $soal = $this->bankSoalRepository->getByLevalAndCategory($levelPeserta, $kategori, $limit);
                            $soalUjian = $soalUjian->merge($soal);
                        }
                    }
                
                    // Shuffle question
                    $soalUjian = $soalUjian->shuffle();
                
                    foreach ($soalUjian as $soal) {
                        $createdJawaban = $this->jawabanUjianRepository->create([
                            'jadwal_ujian_id'   => $jadwalCbt->jadwal_ujian_id,
                            'peserta_id'        => $pesertaId,
                            'soal_id'           => $soal->id,
                            'jawaban_terpilih'  => null,
                            'is_ragu'           => false,
                            'cbt_id'            => $jadwalCbtId,
                        ]);
                        
                        $keyJawabanTerpilih[$soal->id] = $createdJawaban;
                    }
                }
            }
             
            return view('peserta.ujian', compact('jadwalCbt', 'targetTimestamp', 'soalUjian', 'keyJawabanTerpilih'));
        }
    }

    public function saveAnswer(Request $request)
    {
        $validateData = $request->validate([
            'jadwal_ujian_id' => 'required|exists:jadwal_ujian,id',
            'soal_id'         => 'required|exists:banksoal,id',
            'jawaban'         => 'nullable|in:a,b,c,d,e',
            'is_ragu'         => 'boolean',
        ]);
        $jawaban = $this->jawabanUjianRepository->updateOrCreate([
            'jadwal_ujian_id'   => $request->jadwal_ujian_id,
            'peserta_id'        => Auth::user()->peserta->id,
            'soal_id'           => $request->soal_id
        ],
        [
            'jawaban_terpilih'  => strtolower($request->jawaban),
            'is_ragu'           => $request->is_ragu ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jawaban berhasil disimpan',
            'data' => $jawaban
        ], 200);
    }

    public function finish(Request $request)
    {
        $request->validate([
            'jadwal_ujian_id' => 'required|integer',
        ]);
    
        $pesertaId = Auth::user()->peserta->id;
        $jadwalUjianId = $request->jadwal_ujian_id;
    
        DB::beginTransaction(); 
        try {
            $result = $this->calculateAndCompleteExam($pesertaId, $jadwalUjianId);
    
            if (!$result) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Data jawaban peserta tidak ditemukan.'
                ], 404);
            }
    
            DB::commit();
    
            return response()->json([
                'status'       => 'success',
                'message'      => 'Ujian berhasil dikumpulkan.',
                'redirect_url' => route('peserta.dashboard-peserta.index'), 
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal mengoreksi ujian: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function calculateAndCompleteExam(int $pesertaId, int $jadwalUjianId)
    {
        $jawabanPeserta = $this->jawabanUjianRepository->getByPesertaAndJadwalUjian($pesertaId, $jadwalUjianId);
        
        if ($jawabanPeserta->isEmpty()) {
            return null;
        }
    
        $jumlahJawaban = $jawabanPeserta->count();
        $skorSoal = $jumlahJawaban > 0 ? (100 / $jumlahJawaban) : 0;
        $skorFinal = 0;
        
        foreach ($jawabanPeserta as $jawaban) {
            $dataSoal = $jawaban->soal;
    
            if ($dataSoal && $jawaban->jawaban_terpilih === $dataSoal->kunci) {
                $skorFinal += $skorSoal;
                
                $jawaban->update([
                    'is_benar' => true
                ]);
            }
        }
    
        // Ambil data jadwal CBT
        $cbtId = $jawabanPeserta->first()->cbt_id;
        $jadwalCbt = $this->cbtRepository->find($cbtId);
    
        if ($jadwalCbt) {
            $jadwalCbt->update([
                'realtime_selesai'  => now(),
                'status'            => StatusJadwalCbtEnum::SELESAI->value,
                'total_skor'        => $skorFinal
            ]);
        }
    
        return [
            'skor_final' => $skorFinal,
            'jadwal_cbt' => $jadwalCbt
        ];
    }

    public function result(int $jadwalCbtId)
    {   
        $pesertaId = Auth::user()->peserta->id;
        $jadwalCbt = $this->cbtRepository->find($jadwalCbtId, 'id')->first();

        abort_unless($jadwalCbt && (int) $jadwalCbt->peserta_id === (int) $pesertaId, 404);

        $pemberkasan = $this->pemberkasanRepository->getByPesertaAndSchedule($pesertaId, $jadwalCbt->jadwal_ujian_id)->first();
        $tujuanJenjang = $pemberkasan?->tujuan_jenjang;
    
        $jawabanUjian = $this->jawabanUjianRepository
            ->getByPesertaAndJadwalCbt($pesertaId, $jadwalCbtId)
            ->load('soal');
     
        return view('peserta.hasilUjian', compact('jadwalCbt', 'jawabanUjian', 'tujuanJenjang'));
    }
}
