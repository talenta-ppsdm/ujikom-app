<?php

namespace App\Http\Controllers;

use App\Enums\StatusJadwalCbtEnum;
use App\Repositories\CbtRepository;
use App\Repositories\PemberkasanRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RiwayatUjianController extends Controller
{
    protected CbtRepository $cbtRepository;
    protected PemberkasanRepository $pemberkasanRepository;

    public function __construct(CbtRepository $cbtRepository,
        PemberkasanRepository $pemberkasanRepository
    ){
        $this->cbtRepository = $cbtRepository;
        $this->pemberkasanRepository = $pemberkasanRepository;
    }

    public function index(Request $request)
    {
        $pesertaId = Auth::user()->peserta->id;
        $availableYears = $this->cbtRepository->getAvailableYearsByPeserta($pesertaId);
        $selectedYear = $request->tahun;

        if($request->filled('tahun') && $request->tahun !== 'semua'){
            $ujian = $this->cbtRepository->getByPesertaAndYear($pesertaId, $selectedYear);
        }else{
            $ujian = $this->cbtRepository->getByPeserta($pesertaId);
        }

        $countUjian = $ujian->count();

        // Handle recent ujian terjadwal/berlangsung
        $ujian = $this->cbtRepository->getByPeserta($pesertaId);
        $ujianTerjadwal = $ujian->filter(function ($item) {
            $status = strtolower($item->status->value ?? $item->status);
            return in_array($status, [
                StatusJadwalCbtEnum::TERJADWAL->value,
                StatusJadwalCbtEnum::SEDANG_BERLANGSUNG->value,
            ]);
        });

        $recentUjianTerjadwal = $ujianTerjadwal->first();
        $jenjangTujuan = null;
        if ($recentUjianTerjadwal) {
            $pemberkasan = $this->pemberkasanRepository->getByPesertaAndSchedule(
                $recentUjianTerjadwal->peserta_id, 
                $recentUjianTerjadwal->jadwal_ujian_id
            )->first();

            $jenjangTujuan = $pemberkasan?->tujuan_jenjang;
        }

        return view('peserta.riwayatUjian', compact(
            'ujian', 
            'countUjian', 
            'availableYears', 
            'recentUjianTerjadwal', 
            'jenjangTujuan'
        ));
    }
}
