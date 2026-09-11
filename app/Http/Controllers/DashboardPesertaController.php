<?php

namespace App\Http\Controllers;

use App\Enums\RoleUserEnum;
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

        // Ujian terjadwal
        $ujian = $this->jadwalUjianRepository->getByPesertaAndStatus($user->id, StatusJadwalUjianEnum::TERJADWAL->value);

        return view('peserta.beranda', compact('user', 'ujian'));
    }
}
