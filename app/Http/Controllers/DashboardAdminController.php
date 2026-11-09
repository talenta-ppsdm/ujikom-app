<?php

namespace App\Http\Controllers;

use App\Enums\StatusPemberkasanEnum;
use App\Models\Cbt;
use App\Models\Penguji;
use App\Repositories\CbtRepository;
use App\Repositories\JadwalUjianRepository;
use App\Repositories\PesertaRepository;
use App\Repositories\PemberkasanRepository;
use App\Repositories\PengujiRepository;

class DashboardAdminController extends Controller
{
    protected PesertaRepository $pesertaRepository;
    protected PemberkasanRepository $pemberkasanRepository;
    protected JadwalUjianRepository $jadwalUjianRepository;
    protected CbtRepository $cbtRepository;
    protected PengujiRepository $pengujiRepository;

    public function __construct(
        PesertaRepository $pesertaRepository,
        PemberkasanRepository $pemberkasanRepository,
        JadwalUjianRepository $jadwalUjianRepository,
        CbtRepository $cbtRepository,
        PengujiRepository $pengujiRepository,
    ) {
        $this->pesertaRepository = $pesertaRepository;
        $this->pemberkasanRepository = $pemberkasanRepository;
        $this->jadwalUjianRepository = $jadwalUjianRepository;
        $this->cbtRepository = $cbtRepository;
        $this->pengujiRepository = $pengujiRepository;
    }

    public function index()
    {
        $stats = [
            [
                'label' => 'Total Peserta',
                'value' => $this->pesertaRepository->all()->count(),
                'icon' => 'bi-people',
                'link' => route('admin.peserta.index'),
                'link_label' => 'Lihat semua peserta',
            ],
            [
                'label' => 'Menunggu Verifikasi',
                'value' => $this->pemberkasanRepository->getByStatus([
                    StatusPemberkasanEnum::VERIFYING->value,
                    StatusPemberkasanEnum::SUBMIT_PORTOFOLIO->value,
                ])->get()->count(),
                'icon' => 'bi-clipboard-check',
                'link' => route('admin.verifikasi-pemberkasan.index'),
                'link_label' => 'Lihat pendaftar',
            ],
            [
                'label' => 'Lolos Verifikasi',
                'value' => $this->pemberkasanRepository->getByStatus([
                    StatusPemberkasanEnum::APPROVED->value,
                ])->get()->count(),
                'icon' => 'bi-person-check',
                'link' => route('admin.verifikasi-pemberkasan.index'),
                'link_label' => 'Lihat pendaftar',
            ],
            [
                'label' => 'Jadwal Ujian',
                'value' => $this->jadwalUjianRepository->all()->count(),
                'icon' => 'bi-calendar3',
                'link' => route('admin.jadwal-ujian.index'),
                'link_label' => 'Lihat jadwal ujian',
            ],
            [
                'label' => 'Sesi CBT',
                'value' => $this->cbtRepository->all()->count(),
                'icon' => 'bi-pc-display',
                'link' => route('admin.jadwal-cbt.index'),
                'link_label' => 'Lihat jadwal CBT',
            ],
            [
                'label' => 'Penguji Aktif',
                'value' => $this->pengujiRepository->all()->count(),
                'icon' => 'bi-person-check',
                'link' => route('admin.penguji.index'),
                'link_label' => 'Lihat data penguji',
            ],
        ];

        // Handling graph "Statistik Pendaftar Uji Kompetensi"
        $dataUjian = $this->jadwalUjianRepository->withCount('pemberkasan') // Ganti 'pemberkasan' dengan nama method relasi di Model JadwalUjian
        ->latest()
        ->take(5)
        ->get();
        $dataUjian = $dataUjian->reverse()->values();

        $labelUjian = $dataUjian->map(function ($item) {
            return 'Gel. ' . $item->gelombang . ' (' . $item->tahun . ')';
        });

        $jumlahPendaftar = $dataUjian->pluck('pemberkasan_count');

        // Handling graph "Statistik Verifikasi Berkas"
        $jumlahVerifikasi = $this->pemberkasanRepository->getByStatus([StatusPemberkasanEnum::VERIFYING->value])->count();
        $jumlahApproved = $this->pemberkasanRepository->getByStatus([StatusPemberkasanEnum::APPROVED->value, StatusPemberkasanEnum::SUBMIT_PORTOFOLIO->value])->count();
        $jumlahRejected = $this->pemberkasanRepository->getByStatus([StatusPemberkasanEnum::REJECTED->value])->count();
        $jumlahVerifikasiBerkas = [
            'Lolos verifikasi' => $jumlahApproved,
            'Tidak lolos verifikasi' => $jumlahVerifikasi,
            'Belum diverifikasi' => $jumlahRejected
        ];

        // Handling latest registrant
        $latestRegistrants = $this->pemberkasanRepository
            ->getByStatus([StatusPemberkasanEnum::VERIFYING->value])
            ->with('peserta')
            ->orderByDesc('tanggal_daftar')
            ->take(5)
            ->get();

        // Handling latest CBT schedule
        $latestCbtSchedules = $this->cbtRepository
            ->orderBy('tanggal_ujian', 'desc')
            ->with(['peserta', 'jadwalUjian'])
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'labelUjian', 
            'jumlahPendaftar',
            'jumlahVerifikasiBerkas',
            'latestRegistrants',
            'latestCbtSchedules',
        ));
    }
}
