<?php

namespace App\Http\Controllers;

use App\Repositories\JadwalCbtRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RiwayatUjianController extends Controller
{
    protected JadwalCbtRepository $jadwalCbtRepository;

    public function __construct(JadwalCbtRepository $jadwalCbtRepository)
    {
        $this->jadwalCbtRepository = $jadwalCbtRepository;
    }

    public function index(Request $request)
    {
        $pesertaId = Auth::id();
        $availableYears = $this->jadwalCbtRepository->getAvailableYearsByPeserta($pesertaId);
        $selectedYear = $request->tahun;

        if($request->filled('tahun') && $request->tahun !== 'semua'){
            $ujian = $this->jadwalCbtRepository->getByPesertaAndYear($pesertaId, $selectedYear);
        }else{
            $ujian = $this->jadwalCbtRepository->getByPeserta($pesertaId);
        }

        $countUjian = $ujian->count();
        return view('peserta.riwayatUjian', compact('ujian', 'countUjian', 'availableYears'));
    }
}
