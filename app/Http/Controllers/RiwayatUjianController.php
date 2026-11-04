<?php

namespace App\Http\Controllers;

use App\Repositories\CbtRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RiwayatUjianController extends Controller
{
    protected CbtRepository $cbtRepository;

    public function __construct(CbtRepository $cbtRepository)
    {
        $this->cbtRepository = $cbtRepository;
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
        return view('peserta.riwayatUjian', compact('ujian', 'countUjian', 'availableYears'));
    }
}
