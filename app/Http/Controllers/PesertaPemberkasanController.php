<?php

namespace App\Http\Controllers;

use App\Enums\StatusPemberkasanEnum;
use App\Repositories\JadwalPemberkasanRepository;
use App\Repositories\PemberkasanRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesertaPemberkasanController extends Controller
{
    protected PemberkasanRepository $pemberkasanRepository;
    protected JadwalPemberkasanRepository $jadwalPemberkasanRepository;

    public function __construct(
        PemberkasanRepository $pemberkasanRepository,
        JadwalPemberkasanRepository $jadwalPemberkasanRepository,
    )
    {
        $this->pemberkasanRepository = $pemberkasanRepository;
        $this->jadwalPemberkasanRepository = $jadwalPemberkasanRepository;
    }

    public function index()
    {
        $peserta = Auth::user()->peserta;
        $idPserta = $peserta->id;

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

        // Check jadwal pemberkasan
        $activePemberkasanSchedule = $this->jadwalPemberkasanRepository->getActiveSchedule();

        // Check peserta already submit pemberkasan
        $verifyingPemberkasan = $this->pemberkasanRepository->getByPesertaAndScheduleWithStatus($idPserta, $activePemberkasanSchedule->id, StatusPemberkasanEnum::VERIFYING->value);
 
        return view('peserta.pemberkasan', compact('isProfileIncomplete', 'activePemberkasanSchedule', 'verifyingPemberkasan'));
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'sk_golongan'   => 'required|file|mimes:pdf,jpg,jpeg,png|max:1024',
            'sk_jabatan'    => 'required|file|mimes:pdf|max:1024',
        ],[
            'sk_golongan.required'  => 'SK Golongan wajib diunggah.',
            'sk_golongan.file'  => 'SK Golongan wajib berupa file.',
            'sk_golongan.mimes'  => 'SK Golongan harus berformat PDF, JPG, JPEG atau PNG.',
            'sk_golongan.max'  => 'Ukuran file maksimal 1MB.',

            'sk_jabatan.required'  => 'SK Jabatan wajib diunggah.',
            'sk_jabatan.file'  => 'SK Jabatan wajib berupa file.',
            'sk_jabatan.mimes'  => 'SK Jabatan harus berformat PDF, JPG, JPEG atau PNG.',
            'sk_jabatan.max'  => 'Ukuran file maksimal 1MB.',
        ]);

        try {
            // Check if the document quota is full or not
            $activePemberkasan = $this->jadwalPemberkasanRepository->getActiveSchedule();
            $jumlahPendaftar = $this->pemberkasanRepository->getByScheduleandStatus($activePemberkasan->id, [StatusPemberkasanEnum::APPROVED->value, StatusPemberkasanEnum::VERIFYING->value])->count();
            if ($jumlahPendaftar >= $activePemberkasan->kuota) {
                return back()->withInput()->withErrors([
                    'error' => "Kouta pendaftaran telah terpenuhi"
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
                    
                    $paths[$field] = $file->storeAs('peserta/pemberkasan', $filename, 'public');
                }
            }

            // Hanlde save data on database
            $this->pemberkasanRepository->create([
                'file_sk_jabatan'       => $paths['sk_jabatan'] ?? null,
                'file_sk_golongan'      => $paths['sk_golongan'] ?? null,
                'peserta_id'            => Auth::id(),
                'user_id'               => Auth::id(),
                'status'                => StatusPemberkasanEnum::VERIFYING->value,
                'jadwal_pemberkasan_id' => $activePemberkasan->id,
                'tanggal_daftar'        => now(),
                // 'nomor_urut'            => $lastNomorUrut + 1,
            ]);
            
            return redirect()->route('peserta.pemberkasan.index');
        }catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }
}
