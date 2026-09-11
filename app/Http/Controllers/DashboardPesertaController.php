<?php

namespace App\Http\Controllers;

use App\Enums\RoleUserEnum;
use App\Enums\StatusJadwalUjianEnum;
use App\Enums\TujuanUjianEnum;
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

        // Ujian terjadwal
        $ujian = $this->jadwalUjianRepository->getByPesertaAndStatus($user->id, StatusJadwalUjianEnum::TERJADWAL->value);
      
        // Handling show jenjang_tujuan or jabatan_tujuan
        if ($ujian->tujuan_ujian->value == TujuanUjianEnum::KENAIKAN_JENJANG->value) {
            $jenjangJabatanTujuan = $ujian->jejang_tujuan;
            $lebelJenjangJabatanTujuan = 'Jenjang Dituju';
        }elseif ($ujian->tujuan_ujian->value == TujuanUjianEnum::PERPINDAHAN_JABATAN->value) {
            $jenjangJabatanTujuan = $ujian->jabatan_tujuan;
            $lebelJenjangJabatanTujuan = 'Jabatan Dituju';
        }else {
            $lebelJenjangJabatanTujuan = '-';
            $jenjangJabatanTujuan = '-';
        }



        return view('peserta.beranda', compact(
            'user', 
            'ujian',
            'lebelJenjangJabatanTujuan',
            'jenjangJabatanTujuan'
        ));
    }
}
