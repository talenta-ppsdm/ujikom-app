<?php

namespace App\Http\Controllers;

use App\Enums\StatusPemberkasanEnum;
use App\Repositories\JadwalUjianRepository;
use App\Repositories\PemberkasanRepository;
use App\Repositories\PesertaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesertaPemberkasanController extends Controller
{
    protected PemberkasanRepository $pemberkasanRepository;
    protected JadwalUjianRepository $jadwalUjianRepository;
    protected PesertaRepository $pesertaRepository;

    public function __construct(
        PemberkasanRepository $pemberkasanRepository,
        JadwalUjianRepository $jadwalUjianRepository,
        PesertaRepository $pesertaRepository
    )
    {
        $this->pemberkasanRepository = $pemberkasanRepository;
        $this->jadwalUjianRepository = $jadwalUjianRepository;
        $this->pesertaRepository = $pesertaRepository;
    }

    public function index()
    {
        $peserta = Auth::user()->peserta;

        // Checkprofil data
        $requiredProfileFields = [
            'nama',
            'nip',
            'golongan',
            'jabatan',
            'unit',
            'instansi',
            'telepon',
            'email',
        ];
        $isProfileIncomplete = !$peserta;

        if ($peserta) {
            foreach ($requiredProfileFields as $field) {
                if (blank($peserta->{$field})) {
                    $isProfileIncomplete = true;
                    break;
                }
            }
        }

        $activeUjianSchedule = $this->jadwalUjianRepository->getActiveSchedule();
        $isQuotaFull = false;
        $jumlahPendaftar = 0;
        $verifyingPemberkasan = null;
        $approvedPemberkasan = null;
        $verifyingPortofolio = null;

        if ($activeUjianSchedule) {
            $jumlahPendaftar = $this->pemberkasanRepository
                ->getByScheduleandStatus($activeUjianSchedule->id, [
                    StatusPemberkasanEnum::APPROVED->value,
                    StatusPemberkasanEnum::VERIFYING->value,
                    StatusPemberkasanEnum::SUBMIT_PORTOFOLIO->value,
                ])
                ->count();
            $isQuotaFull = $jumlahPendaftar >= $activeUjianSchedule->kuota;

            if ($peserta) {
                // Check peserta already submit pemberkasan awal
                $verifyingPemberkasan = $this->pemberkasanRepository->getByPesertaAndScheduleWithStatus($peserta->id, $activeUjianSchedule->id, StatusPemberkasanEnum::VERIFYING->value);

                // Check pemberkasan awal already approved
                $approvedPemberkasan = $this->pemberkasanRepository->getByPesertaAndScheduleWithStatus($peserta->id, $activeUjianSchedule->id, StatusPemberkasanEnum::APPROVED->value);

                // Check peserta already submit pemberkasan portofolio
                $verifyingPortofolio = $this->pemberkasanRepository->getByPesertaAndScheduleWithStatus($peserta->id, $activeUjianSchedule->id, StatusPemberkasanEnum::SUBMIT_PORTOFOLIO->value);
            }
        }

        return view('peserta.pemberkasan', compact(
            'isProfileIncomplete',
            'activeUjianSchedule',
            'isQuotaFull',
            'verifyingPemberkasan',
            'approvedPemberkasan',
            'verifyingPortofolio',
        ));
    }

    public function storeAwal(Request $request)
    {
        $validateData = $request->validate([
            'sk_golongan'     => 'required|file|mimes:pdf,jpg,jpeg,png|max:1024',
            'sk_jabatan'      => 'required|file|mimes:pdf|max:1024',
            'tujuan_ujian'    => 'required|string',
            'tujuan_jenjang'  => 'required|string',
        ],[
            'sk_golongan.required'  => 'SK Golongan wajib diunggah.',
            'sk_golongan.file'      => 'SK Golongan wajib berupa file.',
            'sk_golongan.mimes'     => 'SK Golongan harus berformat PDF, JPG, JPEG atau PNG.',
            'sk_golongan.max'       => 'Ukuran file maksimal 1MB.',

            'sk_jabatan.required'   => 'SK Jabatan wajib diunggah.',
            'sk_jabatan.file'       => 'SK Jabatan wajib berupa file.',
            'sk_jabatan.mimes'      => 'SK Jabatan harus berformat PDF, JPG, JPEG atau PNG.',
            'sk_jabatan.max'        => 'Ukuran file maksimal 1MB.',
        ]);

        try {
            // Check if the document quota is full or not
            $activeUjian = $this->jadwalUjianRepository->getActiveSchedule();
            if (!$activeUjian) {
                return back()->withInput()->withErrors([
                    'error' => 'Tidak ada jadwal pendaftaran yang sedang dibuka.',
                ]);
            }

            $jumlahPendaftar = $this->pemberkasanRepository->getByScheduleandStatus($activeUjian->id, [StatusPemberkasanEnum::APPROVED->value, StatusPemberkasanEnum::VERIFYING->value])->count();
            if ($jumlahPendaftar >= $activeUjian->kuota) {
                return back()->withInput()->withErrors([
                    'error' => 'Kuota pendaftaran telah penuh.',
                ]);
            }

            // Handle save file on storage
            $nip = Auth::user()->nip ?? Auth::id();
            $timestamp = time();
            $paths = [];
            
            $files = [
                'sk_golongan' => 'SK_Golongan',
                'sk_jabatan'  => 'SK_Jabatan',
            ];
        
            foreach ($files as $field => $prefix) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    
                    $filename = "{$prefix}_{$nip}_{$timestamp}." . $file->getClientOriginalExtension();
                    
                    $paths[$field] = $file->storeAs('peserta/pemberkasan/' . $nip, $filename, 'public');
                }
            }

            // Hanlde save data on database
            $this->pemberkasanRepository->create([
                'file_sk_jabatan'   => $paths['sk_jabatan'] ?? null,
                'file_sk_golongan'  => $paths['sk_golongan'] ?? null,
                'peserta_id'        => Auth::user()->peserta->id,
                'status'            => StatusPemberkasanEnum::VERIFYING->value,
                'jadwal_ujian_id'   => $activeUjian->id,
                'tanggal_daftar'    => now(),
                'tujuan_ujian'      => strtolower($validateData['tujuan_ujian']),
                'tujuan_jenjang'    => strtolower($validateData['tujuan_jenjang']),
            ]);
            
            return redirect()->route('peserta.pemberkasan.index');
        }catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function storePortofolio(Request $request)
    {
        $validateData = $request->validate([
            'portofolio'    => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'praasesmen'    => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'paparan'       => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ],[
            'portofolio.required'  => 'Portofolio wajib diunggah.',
            'portofolio.file'      => 'Portofolio wajib berupa file.',
            'portofolio.mimes'     => 'Portofolio harus berformat PDF, JPG, JPEG atau PNG.',
            'portofolio.max'       => 'Ukuran file maksimal 5MB.',

            'praasesmen.required'   => 'Pra Asesmen wajib diunggah.',
            'praasesmen.file'       => 'Pra Asesmen wajib berupa file.',
            'praasesmen.mimes'      => 'Pra Asesmen harus berformat PDF, JPG, JPEG atau PNG.',
            'praasesmen.max'        => 'Ukuran file maksimal 5MB.',

            'paparan.required'   => 'Bahan Paparan wajib diunggah.',
            'paparan.file'       => 'Bahan Paparan wajib berupa file.',
            'paparan.mimes'      => 'Bahan Paparan harus berformat PDF, JPG, JPEG atau PNG.',
            'paparan.max'        => 'Ukuran file maksimal 5MB.',
        ]);

        try {
            // Handle save file on storage
            $nip = Auth::user()->nip ?? Auth::id();
            $timestamp = time();
            $paths = [];
            
            $files = [
                'portofolio' => 'SK_Golongan',
                'praasesmen'  => 'Pra_Asesmen',
                'paparan'    => 'Bahan_Paparan',
            ];
        
            foreach ($files as $field => $prefix) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    
                    $filename = "{$prefix}_{$nip}_{$timestamp}." . $file->getClientOriginalExtension();
                    
                    $paths[$field] = $file->storeAs('peserta/pemberkasan/' . $nip, $filename, 'public');
                }
            }

            // Handle pemberkasan data
            $pesertaId = Auth::user()->peserta->id;
            $pemberkasan = $this->pemberkasanRepository->getByPesertaAndStatus($pesertaId, StatusPemberkasanEnum::APPROVED->value);

            // Hanlde save data on database
            $this->pemberkasanRepository->update([
                'file_portofolio'   => $paths['portofolio'] ?? null,
                'file_praasesmen'  => $paths['praasesmen'] ?? null,
                'file_paparan'    => $paths['paparan'] ?? null,
                'status'            => StatusPemberkasanEnum::SUBMIT_PORTOFOLIO->value,
            ], $pemberkasan->id);
            
            return redirect()->route('peserta.pemberkasan.index');
        }catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }
}
