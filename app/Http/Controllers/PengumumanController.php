<?php

namespace App\Http\Controllers;

use App\Enums\StatusPengumumanEnum;
use App\Repositories\PengumumanPesertaRepository;
use App\Repositories\PengumumanRepository;
use App\Repositories\PesertaRepository;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    protected PengumumanRepository $pengumumanRepository;
    protected PesertaRepository $pesertaRepository;
    protected PengumumanPesertaRepository $pengumumanPesertaRepository;

    public function __construct(
        PengumumanRepository $pengumumanRepository, 
        PesertaRepository $pesertaRepository,
        PengumumanPesertaRepository $pengumumanPesertaRepository
    )
    {
        $this->pengumumanRepository = $pengumumanRepository;
        $this->pesertaRepository = $pesertaRepository;
        $this->pengumumanPesertaRepository = $pengumumanPesertaRepository;
    }

    public function index()
    {
        $pengumuman = $this->pengumumanRepository->all();
        $pengumuman->loadCount('penerima');
        $allPeserta = $this->pesertaRepository->all();
        return view('pengumuman', compact('pengumuman', 'allPeserta'));
    }

    public function store(Request $request){
        $validatedData = $request->validate([
            'judul'         => 'required|string',
            'konten'        => 'required|string',
            'tgl_terbit'    => 'required|date',
            'tgl_berakhir'  => 'nullable|date|after_or_equal:tgl_terbit',
            'peserta_ids'   => 'required|array|min:1',
            'peserta_ids.*' => 'integer|exists:peserta,id',
        ]);

        try {
            $recipientIds = $validatedData['peserta_ids'] ?? [];
            unset($validatedData['peserta_ids']);
            
            $validatedData['judul'] = strtolower($validatedData['judul']);
            $validatedData['status'] = StatusPengumumanEnum::AKTIF->value;

            $pengumuman = $this->pengumumanRepository->create($validatedData);

            if (!empty($recipientIds)) {
                foreach ($recipientIds as $pesertaId) {
                    $this->pengumumanPesertaRepository->create([
                        'pengumuman_id' => $pengumuman->id,
                        'peserta_id'    => $pesertaId,
                        'is_read'       => false,
                    ]);
                }
            }   
            return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('pengumuman.index')->with('error', 'Pengumuman gagal ditambahkan');
        }
    }

    public function update(Request $request, int $id)
    {
        $validatedData = $request->validate([
            'judul' => 'required|string',
            'konten' => 'required|string',
            'tgl_terbit' => 'required|date',
            'tgl_berakhir' => 'nullable|date|after_or_equal:tgl_terbit',
            'peserta_ids' => 'required|array|min:1',
            'peserta_ids.*' => 'integer|exists:peserta,id',
        ]);

        try {
            $recipientIds = $validatedData['peserta_ids'] ?? [];
            unset($validatedData['peserta_ids']);
            
            $validatedData['judul'] = strtolower($validatedData['judul']);

            $pengumuman = $this->pengumumanRepository->update($validatedData, $id);

            if (!empty($recipientIds)) {
                $pengumuman->penerima()->delete();

                foreach ($recipientIds as $pesertaId) {
                    $this->pengumumanPesertaRepository->create([
                        'pengumuman_id' => $pengumuman->id,
                        'peserta_id'    => $pesertaId,
                        'is_read'       => false,
                    ]);
                }
            }   
            return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->route('pengumuman.index')->with('error', 'Pengumuman gagal diubah');
        }
    }

    public function destroy(int $id){
        $this->pengumumanRepository->delete($id);

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dihapus');
    }
}
