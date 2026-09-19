<?php

namespace App\Http\Controllers;

use App\Enums\JenjangJabatanEnum;
use App\Enums\StatusJadwalUjianEnum;
use App\Repositories\JadwalUjianRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardPesertaController extends Controller
{
    protected UserRepository $userRepository;
    protected JadwalUjianRepository $jadwalUjianRepository;

    public function __construct(
        UserRepository $userRepository,
        JadwalUjianRepository $jadwalUjianRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->jadwalUjianRepository = $jadwalUjianRepository;
    }

    public function index()
    {
        $user = $this->userRepository->with('peserta')->find(Auth::id());
        $ujian = $this->jadwalUjianRepository->getByPeserta($user->id);

        $statusUjian = strtolower($ujian->status);

        if ($statusUjian == StatusJadwalUjianEnum::TERJADWAL || $statusUjian == StatusJadwalUjianEnum::SEDANG_BERLANGSUNG) {
             if ($ujian->jenjang_tujuan == JenjangJabatanEnum::AHLI_PERTAMA->value) {
                $level = 'level 1';
            }elseif ($ujian->jenjang_tujuan == JenjangJabatanEnum::AHLI_MUDA->value) {
                $level = 'level 2 dan 3';
            }elseif ($ujian->jenjang_tujuan == JenjangJabatanEnum::AHLI_MADYA->value) {
                $level = 'level 4';
            }elseif ($ujian->jenjang_tujuan == JenjangJabatanEnum::AHLI_MUDA->value) {
                $level = 'level 5';
            }else{
                $level = '-';
            }
        }else {
            $level = '-';
        }

        return view('peserta.beranda', compact(
            'user', 
            'ujian',
            'level',
            'statusUjian',
        ));
    }
}
