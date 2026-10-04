<?php

namespace App\Http\Controllers;

use App\Repositories\PengumumanRepository;
use App\Repositories\PengumumanPesertaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RiwayatPengumumanController extends Controller
{
    protected PengumumanRepository $pengumumanRepository;
    protected PengumumanPesertaRepository $pengumumanPesertaRepository;

    public function __construct(
        PengumumanRepository $pengumumanRepository,
        PengumumanPesertaRepository $pengumumanPesertaRepository
    )
    {
        $this->pengumumanRepository = $pengumumanRepository;
        $this->pengumumanPesertaRepository = $pengumumanPesertaRepository;
    }

    public function index()
    {
        $activePengumuman = $this->pengumumanRepository->getPesertaActivePengumuman(Auth::id());
        
        return view('peserta.riwayatPengumuman', compact('activePengumuman'));
    }

    public function markAsRead(int $pengumumanId){
        $pesertaId = Auth::id();

        $pengumumanPeserta = $this->pengumumanPesertaRepository->getByPengumumanAndPeserta($pengumumanId, $pesertaId);
        $this->pengumumanPesertaRepository->update(['is_read' => true], $pengumumanPeserta->id);

        return response()->json(['success' => true]);
    }
}
