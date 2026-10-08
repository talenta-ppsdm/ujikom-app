<?php

namespace App\Http\Controllers;

use App\Repositories\PesertaRepository;;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PesertaProfilController extends Controller
{
    protected PesertaRepository $pesertaRepository;

    public function __construct(PesertaRepository $pesertaRepository)
    {
        $this->pesertaRepository = $pesertaRepository;
    }

    public function index()
    {
        $userId = Auth::id();
        $peserta = $this->pesertaRepository->findByUserId($userId);

        return view('peserta.profil', compact('peserta'));
    }

    public function update(Request $request, int $pesertaId)
    {
        $validatedData = $request->validate([
            'nama'      => 'required|string|max:255',
            'nip'       => 'required|string|max:20',
            'golongan'  => 'required|string|max:10',
            'jabatan'   => 'required|string|max:255',
            'unit'      => 'required|string|max:255',
            'instansi'  => 'required|string|max:255',
            'telepon'   => 'required|string|max:20',
            'email'     => 'required|email|max:255',
        ],[
            'nama.required'     => 'Nama wajib diisi.',
            'nip.required'      => 'NIP wajib diisi.',
            'golongan.required' => 'Golongan wajib diisi.',
            'jabatan.required'  => 'Jabatan wajib diisi.',
            'unit.required'     => 'Unit wajib diisi.',
            'instansi.required' => 'Instansi wajib diisi.',
            'telepon.required'  => 'Telepon wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
        ]);

        try {
            $peserta = $this->pesertaRepository->find($pesertaId);
            if (!$peserta) {
                return redirect()->back()->with('error', 'Peserta tidak ditemukan.');
            }
            
            $this->pesertaRepository->update($validatedData, $pesertaId);
        }catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'email' => $e->getMessage()
            ]);
        }
        return redirect()->route('peserta.profil')->with('success', 'Profil berhasil diperbarui.');
    }
}
