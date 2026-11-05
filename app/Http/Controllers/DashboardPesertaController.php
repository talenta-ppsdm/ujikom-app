<?php

namespace App\Http\Controllers;

use App\Enums\StatusJadwalCbtEnum;
use App\Repositories\JadwalUjianRepository;
use App\Repositories\CbtRepository;
use App\Repositories\PengumumanRepository;
use App\Repositories\UserRepository;
use App\Repositories\PesertaRepository;
use App\Repositories\PemberkasanRepository;
use Illuminate\Support\Facades\Auth;

class DashboardPesertaController extends Controller
{
    protected UserRepository $userRepository;
    protected CbtRepository $cbtRepository;
    protected PengumumanRepository $pengumumanRepository;
    protected PesertaRepository $pesertaRepository;
    protected JadwalUjianRepository $jadwalUjianRepository;
    protected PemberkasanRepository $pemberkasanRepository;

    public function __construct(
        UserRepository $userRepository,
        CbtRepository $cbtRepository,
        PengumumanRepository $pengumumanRepository,
        PesertaRepository $pesertaRepository,
        JadwalUjianRepository $jadwalUjianRepository,
        PemberkasanRepository $pemberkasanRepository
    )
    {
        $this->userRepository               = $userRepository;
        $this->cbtRepository                = $cbtRepository;
        $this->pengumumanRepository         = $pengumumanRepository;
        $this->pesertaRepository            = $pesertaRepository;
        $this->jadwalUjianRepository        = $jadwalUjianRepository;
        $this->pemberkasanRepository        = $pemberkasanRepository;
    }

    public function index()
    {
        $user = $this->userRepository->with('peserta')->find(Auth::id());
        $ujian = $this->cbtRepository->getByPeserta($user->peserta->id);
        $ujianTerjadwal = $ujian->filter(function ($item) {
            $status = strtolower($item->status->value ?? $item->status);
            return in_array($status, [
                StatusJadwalCbtEnum::TERJADWAL->value,
                StatusJadwalCbtEnum::SEDANG_BERLANGSUNG->value,
            ]);
        });
        $ujianSelesai = $ujian->filter(function ($item) {
            $status = strtolower($item->status->value ?? $item->status);
            return $status === StatusJadwalCbtEnum::SELESAI->value;
        });
        
        $jenjangTujuan = null;
        $recentUjian = $ujian->take(2)->values()->map(function ($item) {
            $pemberkasan = $this->pemberkasanRepository->getByPesertaAndSchedule(
                $item->peserta_id, 
                $item->jadwal_ujian_id
            )->first();

            $pemberkasanObj = $pemberkasan instanceof \Illuminate\Support\Collection ? $pemberkasan->first() : $pemberkasan;

            $item->tujuan_jenjang = $pemberkasanObj?->tujuan_jenjang;

            return $item;
        });
        
        
        $recentUjianTerjadwal = $ujianTerjadwal->first();
        if ($recentUjianTerjadwal) {
            $pemberkasan = $this->pemberkasanRepository->getByPesertaAndSchedule(
                $recentUjianTerjadwal->peserta_id, 
                $recentUjianTerjadwal->jadwal_ujian_id
            )->first();

            $jenjangTujuan = $pemberkasan?->tujuan_jenjang;
        }

        $pengumuman = $this->pengumumanRepository->getPesertaActivePengumuman($user->id)
            ->sortByDesc('tgl_terbit')->take(4)->values();

        // Check participant's personal data
        $peserta = $user->peserta;
        $isProfileIncomplete = false;
        if(!$peserta->nama || !$peserta->golongan || !$peserta->unit || !$peserta->instansi || !$peserta->telepon || !$peserta->email) {
            $isProfileIncomplete = true;
        }

        // Check jadwal pemberkasan
        $activePemberkasanSchedule = $this->jadwalUjianRepository->getActiveSchedule();
    
        return view('peserta.beranda', compact(
            'user', 
            'ujian',
            'ujianTerjadwal', // untuk rekap ujian
            'ujianSelesai', // untuk rekap ujian
            'recentUjian', // untuk ujian terbaru
            'jenjangTujuan',
            'pengumuman',
            'isProfileIncomplete',
            'activePemberkasanSchedule'
        ));
    }
}
