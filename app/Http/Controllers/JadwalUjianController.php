<?php

namespace App\Http\Controllers;

use App\Enums\TujuanUjianEnum;
use App\Models\JadwalUjian;
use App\Repositories\JadwalUjianRepository;
use App\Repositories\PengujiRepository;
use App\Repositories\PesertaRepository;
use Illuminate\Http\Request;

class JadwalUjianController extends Controller
{
    protected JadwalUjianRepository $jadwalUjianRepository;
    protected PesertaRepository $pesertaRepository;
    protected PengujiRepository $pengujiRepository;

    public function  __construct(
        JadwalUjianRepository $jadwalUjianRepository,
        PesertaRepository $pesertaRepository,
        PengujiRepository $pengujiRepository
    )
    {
        $this->jadwalUjianRepository = $jadwalUjianRepository;
        $this->pesertaRepository = $pesertaRepository;
        $this->pengujiRepository = $pengujiRepository;
    }

    public function index()
    {
        $jadwalUjian = $this->jadwalUjianRepository->all();
        $peserta = $this->pesertaRepository->all();
        $penguji = $this->pengujiRepository->all();
        $tujuan = TujuanUjianEnum::options();
        $listStatus = JadwalUjian::getStatusLabels();

        return view('jadwalUjian', compact(
            'jadwalUjian', 
            'peserta', 
            'penguji', 
            'tujuan',
            'listStatus'
        ));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'peserta_id' => 'required|exists:peserta,id',
            'tujuan_ujian' => 'nullable|string|max:255',
            'penguji_id' => 'required|exists:penguji,id',
            'tanggal_ujian' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'lokasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:255',
            'jenjang_tujuan' => 'nullable|string|max:255',
        ]);

        // count the duration in minutes
        $waktuMulai = \Carbon\Carbon::createFromFormat('H:i', $validatedData['waktu_mulai']);
        $waktuSelesai = \Carbon\Carbon::createFromFormat('H:i', $validatedData['waktu_selesai']);
        $durasi = $waktuMulai->diffInMinutes($waktuSelesai);

        $status = array_key_first(JadwalUjian::getStatusLabels());

        $jabatanTujuan = 'penata kelola perumahan';

        $this->jadwalUjianRepository->create(array_merge($validatedData, [
            'durasi' => $durasi, 
            'status' => $status, 
            'jabatan_tujuan' => $jabatanTujuan
        ]));

        return redirect()->route('jadwal-ujian.index')->with('success', 'Jadwal ujian berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $validatedData = $request->validate([
            'peserta_id' => 'required|exists:peserta,id',
            'tujuan_ujian' => 'nullable|string|max:255',
            'penguji_id' => 'required|exists:penguji,id',
            'tanggal_ujian' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'lokasi' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:255',
            'status' => 'required|in:' . implode(',', array_keys(JadwalUjian::getStatusLabels())),
            'jabatan_tujuan' => 'nullable|string|max:255',
            'jenjang_tujuan' => 'nullable|string|max:255',
        ]);

        // count the duration in minutes
        $waktuMulai = \Carbon\Carbon::createFromFormat('H:i', $validatedData['waktu_mulai']);
        $waktuSelesai = \Carbon\Carbon::createFromFormat('H:i', $validatedData['waktu_selesai']);
        $durasi = $waktuMulai->diffInMinutes($waktuSelesai);

        $jabatanTujuan = 'penata kelola perumahan';

        $this->jadwalUjianRepository->update(array_merge($validatedData, ['durasi' => $durasi, 'jabatan_tujuan' => $jabatanTujuan]), $id);

        return redirect()->route('jadwal-ujian.index')->with('success', 'Jadwal ujian berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $this->jadwalUjianRepository->delete($id);

        return redirect()->route('jadwal-ujian.index')->with('success', 'Jadwal ujian berhasil dihapus.');
    }
}
