<?php

namespace App\Http\Controllers;

use App\Enums\StatusPengumumanEnum;
use App\Enums\TargetPengumumanEnum;
use App\Repositories\PengumumanPesertaRepository;
use App\Repositories\PengumumanRepository;
use App\Repositories\PesertaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $peserta = $this->pesertaRepository->all();
        return view('pengumuman', compact('pengumuman', 'peserta'));
    }

    public function store(Request $request){
        $validateData = $request->validate([
            'judul' => 'required|string',
            'konten' => 'required|string',
            'tipe_target' => 'required|in:semua,peserta terpilih',
            'tgl_terbit' => 'required|date',
            'tgl_berakhir' => 'nullable|date|after_or_equal:tgl_terbit',
        ]);

        $pengumumanData = [
            'judul'         => strtolower($validateData['judul']),
            'konten'        => $validateData['konten'],
            'tgl_terbit'    => $validateData['tgl_terbit'],
            'tgl_berakhir'  => $validateData['tgl_berakhir'],
            'tipe_target'   => $validateData['tipe_target'],
            'status'        => StatusPengumumanEnum::AKTIF->value,
            'is_read'       => false,
        ];
        $pengumuman = $this->pengumumanRepository->create($pengumumanData);

        // Create pengumuman peserta
        if ($validateData['tipe_target'] === TargetPengumumanEnum::SEMUA->value) {
            $peserta = $this->pesertaRepository->all();
            foreach ($peserta as $dataPeserta) {
                $this->pengumumanPesertaRepository->create([
                    'pengumuman_id' => $pengumuman->id,
                    'peserta_id'    => $dataPeserta->id,
                ]);
            }
        }elseif ($validateData['tipe_target'] === TargetPengumumanEnum::PESERTA_TERPILIH->value) {
            foreach ($request['peserta_ids'] as $idPeserta) {
                $this->pengumumanPesertaRepository->create([
                    'pengumuman_id' => $pengumuman->id,
                    'peserta_id'    => $idPeserta,
                ]);
            }
        }else {
            return redirect()->route('pengumuman.index')->with('error', 'Pengumuman gagal ditambahkan.');
        }

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $validatedData = $request->validate([
            'judul' => 'required|string',
            'konten' => 'required|string',
            'tipe_target' => 'required|in:semua,peserta terpilih',
            'tgl_terbit' => 'required|date',
            'tgl_berakhir' => 'nullable|date|after_or_equal:tgl_terbit',
            'peserta_ids' => 'required_if:tipe_target,peserta terpilih',
            'peserta_ids.*' => 'integer|exists:peserta,id',
        ]);

        $recipientIds = $validatedData['peserta_ids'] ?? [];
        unset($validatedData['peserta_ids']);

        DB::transaction(function () use ($validatedData, $id, $recipientIds) {
            $pengumuman = $this->pengumumanRepository->update($validatedData, $id);
            $pengumuman->penerima()->delete();

            if ($validatedData['tipe_target'] === TargetPengumumanEnum::SEMUA->value) {
                $recipientIds = $this->pesertaRepository->all()->pluck('id')->all();
            }

            foreach ($recipientIds as $pesertaId) {
                $this->pengumumanPesertaRepository->create([
                    'pengumuman_id' => $pengumuman->id,
                    'peserta_id' => $pesertaId,
                ]);
            }
        });

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(int $id){
        $this->pengumumanRepository->delete($id);

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
