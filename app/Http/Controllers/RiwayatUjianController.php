<?php

namespace App\Http\Controllers;

use App\Repositories\JadwalUjianRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RiwayatUjianController extends Controller
{
    protected JadwalUjianRepository $jadwalUjianRepository;

    public function __construct(JadwalUjianRepository $jadwalUjianRepository)
    {
        $this->jadwalUjianRepository = $jadwalUjianRepository;
    }

    public function index(Request $request)
    {
        $pesertaId = Auth::id();
        $availableYears = $this->jadwalUjianRepository->getAvailableYearsByPeserta($pesertaId);
        $selectedYear = $request->tahun;

        if($request->filled('tahun') && $request->tahun !== 'semua'){
            $ujian = $this->jadwalUjianRepository->getByPesertaAndYear($pesertaId, $selectedYear);
        }else{
            $ujian = $this->jadwalUjianRepository->getByPeserta($pesertaId);
        }

        $countUjian = $ujian->count();
        return view('peserta.riwayatUjian', compact('ujian', 'countUjian', 'availableYears'));
    }
}
