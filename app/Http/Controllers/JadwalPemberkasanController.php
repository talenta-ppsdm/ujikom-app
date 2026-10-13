<?php

namespace App\Http\Controllers;

use App\Repositories\JadwalPemberkasanRepository;
use Illuminate\Http\Request;

class JadwalPemberkasanController extends Controller
{
    protected JadwalPemberkasanRepository $jadwalPemberkasanRepository;

    public function __construct(JadwalPemberkasanRepository $jadwalPemberkasanRepository)
    {
        $this->jadwalPemberkasanRepository = $jadwalPemberkasanRepository;
    }

    public function index()
    {
        $jadwalPemberkasan = $this->jadwalPemberkasanRepository->all();
        return view('jadwalPemberkasan', compact('jadwalPemberkasan'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validateData = $request->validate([
            'gelombang'     => 'required',
            'tahun'         => 'required',
            'kuota'         => 'required|integer|min:1',
            'periode_awal'  => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
        ],[
            'kuota.min'                     => 'Kuota minimal adalah 1 peserta.',
            'periode_akhir.after_or_equal'  => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);

        try {
            // Cek apakah ada jadwal pemberkasan yang aktif
            $activePemberkasan = $this->jadwalPemberkasanRepository->getActiveSchedule();
            if ($activePemberkasan) {
                return back()->withInput()->withErrors([
                    'error' => 'Terdapat jadwal pemberkasan yang aktif'
                ]);
            }

            $this->jadwalPemberkasanRepository->create(array_merge($validateData,[
                'status'    => 'aktif'
            ]));

        }catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('jadwal-pemberkasan.index')->with('success', 'Jadwal pemberkasan berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $validateData = $request->validate([
            'gelombang'     => 'required',
            'tahun'         => 'required',
            'kuota'         => 'required|integer|min:1',
            'periode_awal'  => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
        ],[
            'kuota.min'                     => 'Kuota minimal adalah 1 peserta.',
            'periode_akhir.after_or_equal'  => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
        ]);

        try {
            $this->jadwalPemberkasanRepository->update($validateData, $id);

        }catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('jadwal-pemberkasan.index')->with('success', 'Jadwal pemberkasan berhasil diubah.');
    }

    public function destroy(int $id)
    {
        $this->jadwalPemberkasanRepository->delete($id);

        return redirect()->route('jadwal-pemberkasan.index')->with('success', 'Jadwal pemberkasan berhasil dihapus.');
    }
}
