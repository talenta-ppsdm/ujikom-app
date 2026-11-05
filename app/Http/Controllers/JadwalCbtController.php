<?php

namespace App\Http\Controllers;

use App\Enums\StatusJadwalCbtEnum;
use App\Enums\TujuanUjianEnum;
use App\Models\Cbt;
use App\Models\JadwalCbt;
use App\Models\JadwalUjian;
use App\Repositories\CbtRepository;
use App\Repositories\JadwalCbtRepository;
use App\Repositories\JadwalUjianRepository;
use App\Repositories\PengujiRepository;
use App\Repositories\PesertaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalCbtController extends Controller
{
    protected CbtRepository $cbtRepository;
    protected PesertaRepository $pesertaRepository;
    protected PengujiRepository $pengujiRepository;
    protected JadwalUjianRepository $jadwalUjianRepository;

    public function  __construct(
        CbtRepository $cbtRepository,
        PesertaRepository $pesertaRepository,
        PengujiRepository $pengujiRepository,
        JadwalUjianRepository $jadwalUjianRepository,
    )
    {
        $this->cbtRepository         = $cbtRepository;
        $this->pesertaRepository     = $pesertaRepository;
        $this->pengujiRepository     = $pengujiRepository;
        $this->jadwalUjianRepository = $jadwalUjianRepository;
    }

    public function index()
    {
        $jadwalCbt      = $this->cbtRepository->all();
        $peserta        = $this->pesertaRepository->all();
        $tujuan         = TujuanUjianEnum::options();
        $listStatus     = Cbt::getStatusLabels();

        // Handling jadwal ujian
        $jadwalUjian = $this->jadwalUjianRepository->getActiveSchedule();

        return view('/admin/jadwalCbt', compact(
            'jadwalCbt', 
            'peserta', 
            'tujuan',
            'listStatus',
            'jadwalUjian'
        ));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'peserta_id'      => 'required|exists:peserta,id',
            'tanggal_ujian'   => 'required|date',
            'waktu_mulai'     => 'required|date_format:H:i',
            'waktu_selesai'   => 'required|date_format:H:i|after:waktu_mulai',
            'keterangan'      => 'nullable|string|max:255',
            'jadwal_ujian_id' => 'required|exists:jadwal_ujian,id',
        ]);

        $hasActiveUjian = $this->cbtRepository->hasActiveUjian($request->peserta_id);
        if ($hasActiveUjian) {
            if ($hasActiveUjian) {
                return redirect()->back()->with('error', 'Gagal: Terdapat ujian yang sedang berlangsung/terjadwal.');
            }
        }

        // count the duration in minutes
        $waktuMulai = \Carbon\Carbon::createFromFormat('H:i', $validatedData['waktu_mulai']);
        $waktuSelesai = \Carbon\Carbon::createFromFormat('H:i', $validatedData['waktu_selesai']);
        $durasi = $waktuMulai->diffInMinutes($waktuSelesai);

        $status = array_key_first(Cbt::getStatusLabels());

        $this->cbtRepository->create(array_merge($validatedData, [
            'durasi' => $durasi, 
            'status' => $status, 
        ]));

        return redirect()->route('jadwal-cbt.index')->with('success', 'Jadwal ujian berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $validatedData = $request->validate([
            'peserta_id'        => 'required|exists:peserta,id',
            'jadwal_ujian_id'   => 'required|exists:jadwal_ujian,id',
            'status'            => 'required',
            'tanggal_ujian'     => 'required|date',
            'waktu_mulai'       => 'required|date_format:H:i',
            'waktu_selesai'     => 'required|date_format:H:i|after:waktu_mulai',
            'keterangan'        => 'nullable|string|max:255',
        ]);

        try {
            // count the duration in minutes
            $waktuMulai = \Carbon\Carbon::createFromFormat('H:i', $validatedData['waktu_mulai']);
            $waktuSelesai = \Carbon\Carbon::createFromFormat('H:i', $validatedData['waktu_selesai']);
            $durasi = $waktuMulai->diffInMinutes($waktuSelesai);

            $this->cbtRepository->update(array_merge($validatedData, ['durasi' => $durasi]), $id);

            return redirect()->route('jadwal-cbt.index')->with('success', 'Jadwal ujian berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->route('jadwal-cbt.index')->with('error', 'Jadwal ujian gagal diperbarui');
        }
    }

    public function destroy(int $id)
    {
        $this->cbtRepository->delete($id);

        return redirect()->route('jadwal-cbt.index')->with('success', 'Jadwal ujian berhasil dihapus.');
    }
}
