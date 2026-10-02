<?php

namespace App\Http\Controllers;

use App\Enums\JenjangJabatanEnum;
use App\Enums\StatusJadwalUjianEnum;
use App\Repositories\JadwalUjianRepository;
use App\Repositories\PengumumanRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardPesertaController extends Controller
{
    protected UserRepository $userRepository;
    protected JadwalUjianRepository $jadwalUjianRepository;
    protected PengumumanRepository $pengumumanRepository;

    public function __construct(
        UserRepository $userRepository,
        JadwalUjianRepository $jadwalUjianRepository,
        PengumumanRepository $pengumumanRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->jadwalUjianRepository = $jadwalUjianRepository;
        $this->pengumumanRepository = $pengumumanRepository;
    }

    public function index()
    {
        $user = $this->userRepository->with('peserta')->find(Auth::id());
        $ujian = $this->jadwalUjianRepository->getByPeserta($user->id)
            ->sortByDesc('created_at')->take(2)->values();   
            
        $ujianTerjadwal = $ujian->first(function ($itemUjian){
            $statusUjian = strtolower($itemUjian->status);
            return $statusUjian === StatusJadwalUjianEnum::TERJADWAL->value || $statusUjian === StatusJadwalUjianEnum::SEDANG_BERLANGSUNG->value;
        });

        $pengumuman = $this->pengumumanRepository->getPesertaActivePengumuman($user->id)
            ->sortByDesc('tgl_terbit')->take(4)->values();
        
        return view('peserta.beranda', compact(
            'user', 
            'ujian',
            'ujianTerjadwal',
            'pengumuman'
        ));
    }
}
