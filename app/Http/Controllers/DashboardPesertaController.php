<?php

namespace App\Http\Controllers;

use App\Enums\StatusJadwalUjianEnum;
use App\Repositories\JadwalUjianRepository;
use App\Repositories\PengumumanRepository;
use App\Repositories\UserRepository;
use App\Repositories\PesertaRepository;
use Illuminate\Support\Facades\Auth;

class DashboardPesertaController extends Controller
{
    protected UserRepository $userRepository;
    protected JadwalUjianRepository $jadwalUjianRepository;
    protected PengumumanRepository $pengumumanRepository;
    protected PesertaRepository $pesertaRepository;

    public function __construct(
        UserRepository $userRepository,
        JadwalUjianRepository $jadwalUjianRepository,
        PengumumanRepository $pengumumanRepository,
        PesertaRepository $pesertaRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->jadwalUjianRepository = $jadwalUjianRepository;
        $this->pengumumanRepository = $pengumumanRepository;
        $this->pesertaRepository = $pesertaRepository;
    }

    public function index()
    {
        $user = $this->userRepository->with('peserta')->find(Auth::id());
        $ujian = $this->jadwalUjianRepository->getByPeserta($user->id);
        $ujianTerjadwal = $ujian->filter(function ($item) {
            $status = strtolower($item->status->value ?? $item->status);
            return in_array($status, [
                StatusJadwalUjianEnum::TERJADWAL->value,
                StatusJadwalUjianEnum::SEDANG_BERLANGSUNG->value,
            ]);
        });
        $ujianSelesai = $ujian->filter(function ($item) {
            $status = strtolower($item->status->value ?? $item->status);
            return $status === StatusJadwalUjianEnum::SELESAI->value;
        });
               
        $recentUjian = $ujian->take(2)->values();
        $recentUjianTerjadwal = $ujianTerjadwal->first();

        $pengumuman = $this->pengumumanRepository->getPesertaActivePengumuman($user->id)
            ->sortByDesc('tgl_terbit')->take(4)->values();

        // Check participant's personal data
        $peserta = $user->peserta;
        $isProfileIncomplete = false;
        if(!$peserta->nama || !$peserta->golongan || !$peserta->unit || !$peserta->instansi || !$peserta->telepon || !$peserta->email) {
            $isProfileIncomplete = true;
        }
        
        return view('peserta.beranda', compact(
            'user', 
            'ujian',
            'ujianTerjadwal',
            'ujianSelesai',
            'recentUjianTerjadwal',
            'recentUjian',
            'pengumuman',
            'isProfileIncomplete'
        ));
    }
}
