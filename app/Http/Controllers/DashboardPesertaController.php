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
        $ujian = $this->jadwalUjianRepository->getByPeserta($user->id)
            ->sortByDesc('created_at')->take(2)->values();   
            
        $ujianTerjadwal = $ujian->first(function ($itemUjian){
            $statusUjian = strtolower($itemUjian->status);
            return $statusUjian === StatusJadwalUjianEnum::TERJADWAL->value || $statusUjian === StatusJadwalUjianEnum::SEDANG_BERLANGSUNG->value;
        });
        
        return view('peserta.beranda', compact(
            'user', 
            'ujian',
            'ujianTerjadwal'
        ));
    }
}
