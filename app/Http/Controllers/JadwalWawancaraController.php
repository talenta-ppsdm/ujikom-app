<?php

namespace App\Http\Controllers;

use App\Enums\StatusJadwalWawancaraEnum;
use App\Enums\StatusPemberkasanEnum;
use App\Enums\StatusUjianEnum;
use App\Repositories\JadwalUjianRepository;
use App\Repositories\JadwalWawancaraRepository;
use App\Repositories\PemberkasanRepository;
use App\Repositories\PengujiRepository;
use App\Repositories\PesertaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class JadwalWawancaraController extends Controller
{
    protected JadwalWawancaraRepository $jadwalWawancaraRepository;
    protected PesertaRepository $pesertaRepository;
    protected PengujiRepository $pengujiRepository;
    protected JadwalUjianRepository $jadwalUjianRepository;
    protected PemberkasanRepository $pemberkasanRepository;

    public function __construct(JadwalWawancaraRepository $jadwalWawancaraRepository,
        PesertaRepository $pesertaRepository,
        PengujiRepository $pengujiRepository,
        JadwalUjianRepository $jadwalUjianRepository,
        PemberkasanRepository $pemberkasanRepository)
    {
        $this->jadwalWawancaraRepository = $jadwalWawancaraRepository;
        $this->pesertaRepository = $pesertaRepository;
        $this->pengujiRepository = $pengujiRepository;
        $this->jadwalUjianRepository = $jadwalUjianRepository;
        $this->pemberkasanRepository = $pemberkasanRepository;
    }

    public function index()
    {
        $wawancara = $this->jadwalWawancaraRepository->all();

        $peserta = $this->pesertaRepository->all();
        $penguji = $this->pengujiRepository->all();

        return view('/admin/jadwalWawancara', compact('wawancara', 'peserta', 'penguji'));
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'peserta_id'    => 'required|exists:peserta,id',
            'penguji_id'    => 'required|exists:penguji,id',
            'tanggal'       => 'required|date',
            'jam_mulai'     => 'required|date_format:H:i',
            'jam_selesai'   => 'required|date_format:H:i|after:jam_mulai',
            'metode'        => 'required',
            'lokasi_link'   => 'required',
            ],[
                'jam_selesai.after' => 'Jam selesai harus lebih besar daripada jam mulai',
            ]);

        try {
            // Handling jadwal ujian
            $activeJadwalUjian = $this->jadwalUjianRepository->getByStatus(StatusUjianEnum::AKTIF->value)->first();
            $pemberkasan = $this->pemberkasanRepository->getByPesertaAndSchedule($validateData['peserta_id'], $activeJadwalUjian->id)->first();

            if(!$activeJadwalUjian || !$pemberkasan){
                return back()->withInput()->withErrors([
                    'error' => 'Jadwal ujian tidak ditemukan'
                ]);
            }elseif ($activeJadwalUjian->id === $pemberkasan->jadwal_ujian_id) {
                $this->jadwalWawancaraRepository->create(array_merge($validateData,[
                    'jadwal_ujian_id'   => $activeJadwalUjian->id,
                    'status'            => StatusJadwalWawancaraEnum::TERJADWAL->value
                ]));
            }else {
                return back()->withInput()->withErrors([
                    'error' => 'Terjadi kesalahan input'
                ]);
            }
        }catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('admin.jadwal-wawancara.index')->with('success', 'Jadwal wawancara berhasil ditambahkan');
    }

    public function update(Request $request, string $encryptedId)
    {
        $validateData = $request->validate([
            'peserta_id'    => 'required|exists:peserta,id',
            'penguji_id'    => 'required|exists:penguji,id',
            'tanggal'       => 'required|date',
            'jam_mulai'     => 'required|date_format:H:i',
            'jam_selesai'   => 'required|date_format:H:i|after:jam_mulai',
            'metode'        => 'required',
            'lokasi_link'   => 'required',
        ],[
            'jam_selesai.after' => 'Jam selesai harus lebih besar daripada jam mulai',
        ]);
        
        try {
            $id = Crypt::decrypt($encryptedId);

            $jadwalWawancara = $this->jadwalWawancaraRepository->find($id);
            $jadwalWawancara->update($validateData);
        }catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('admin.jadwal-wawancara.index')->with('success', 'Jadwal wawancara berhasil ditambahkan');
    }

    public function destroy(string $encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $this->jadwalWawancaraRepository->delete($id);

        return redirect()->route('admin.jadwal-wawancara.index')->with('success', 'Jadwal wawancara berhasil dihapus');
    }
}
