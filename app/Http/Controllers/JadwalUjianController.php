<?php

namespace App\Http\Controllers;

use App\Repositories\JadwalUjianRepository;
use Illuminate\Http\Request;

class JadwalUjianController extends Controller
{
    protected JadwalUjianRepository $jadwalUjianRepository;

    public function __construct(JadwalUjianRepository $jadwalUjianRepository)
    {
        $this->jadwalUjianRepository = $jadwalUjianRepository;
    }

    public function index()
    {
        $jadwalUjian = $this->jadwalUjianRepository->all();
        return view('/admin/jadwalUjian', compact('jadwalUjian'));
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'gelombang'     => 'required',
            'tahun'         => 'required',
            'kuota'         => 'required|integer|min:1',
            'periode_awal'  => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
            'periode_pemberkasan_awal'  => 'required|date',
            'periode_pemberkasan_akhir' => 'required|date|after_or_equal:periode_pemberkasan_awal',
            'periode_portofolio_awal'  => 'required|date',
            'periode_portofolio_akhir' => 'required|date|after_or_equal:periode_portofolio_awal',
        ],[
            'kuota.min'                                 => 'Kuota minimal adalah 1 peserta.',
            'periode_akhir.after_or_equal'              => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'periode_pemberkasan_akhir.after_or_equal'  => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'periode_portofolio_akhir.after_or_equal'   => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);

        try {
            // Cek apakah ada jadwal pemberkasan yang aktif
            $activeJadwalUjian = $this->jadwalUjianRepository->getActiveSchedule();
            if ($activeJadwalUjian) {
                return back()->withInput()->withErrors([
                    'error' => 'Terdapat jadwal ujian yang aktif'
                ]);
            }

            $this->jadwalUjianRepository->create(array_merge($validateData,[
                'status'    => 'aktif'
            ]));

        }catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('jadwal-ujian.index')->with('success', 'Jadwal ujian berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $validateData = $request->validate([
            'gelombang'                 => 'required',
            'tahun'                     => 'required',
            'kuota'                     => 'required|integer|min:1',
            'periode_awal'              => 'required|date',
            'periode_akhir'             => 'required|date|after_or_equal:periode_awal',
            'periode_pemberkasan_awal'  => 'required|date',
            'periode_pemberkasan_akhir' => 'required|date|after_or_equal:periode_pemberkasan_awal',
            'periode_portofolio_awal'   => 'required|date',
            'periode_portofolio_akhir'  => 'required|date|after_or_equal:periode_portofolio_awal',
            'status'                    => 'required',
        ],[
            'kuota.min'                                 => 'Kuota minimal adalah 1 peserta.',
            'periode_akhir.after_or_equal'              => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'periode_pemberkasan_akhir.after_or_equal'  => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'periode_portofolio_akhir.after_or_equal'   => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);

        try {
            // TODO: klo ada perubahan status ke aktif, cek apakah ada jadwal ujian yang aktif
            $this->jadwalUjianRepository->update($validateData, $id);

        }catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('jadwal-ujian.index')->with('success', 'Jadwal ujian berhasil diubah.');
    }

    public function destroy(int $id)
    {
        $this->jadwalUjianRepository->delete($id);

        return redirect()->route('jadwal-ujian.index')->with('success', 'Jadwal ujian berhasil dihapus.');
    }
}
