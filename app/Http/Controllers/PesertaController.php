<?php

namespace App\Http\Controllers;

use App\Enums\RoleUserEnum;
use App\Repositories\PesertaRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PesertaController extends Controller
{
    protected PesertaRepository $pesertaRepository;
    protected UserRepository $userRepository;

    public function __construct(
        PesertaRepository $pesertaRepository,
        UserRepository $userRepository
    ) {
        $this->pesertaRepository = $pesertaRepository;
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $peserta = $this->pesertaRepository->all();
        return view('peserta', compact('peserta'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama'      => 'required|string|max:255',
            'nip'       => 'required|string|max:20|unique:peserta,nip',
            'golongan'  => 'required|string|max:10',
            'jabatan'   => 'required|string|max:100',
            'unit'      => 'required|string|max:100',
            'instansi'  => 'required|string|max:100',
            'telepon'   => 'nullable|string|max:15',
            'email'     => 'nullable|email|max:255',
        ]);

        // Create user data for the peserta
        $userData = [
            'name' => $validatedData['nama'],
            'email' => $validatedData['email'],
            'password' => bcrypt('peserta2026'), 
            'nip' => $validatedData['nip'],
            'role' => RoleUserEnum::PESERTA->value,
        ];
        $user = $this->userRepository->create($userData);

        // Create peserta by add userId
        $this->pesertaRepository->create(array_merge(
            $validatedData, ['user_id' => $user->id])
        );

        return redirect()->route('admin.peserta.index')->with('success', 'Data Peserta berhasil ditambahkan');
    }

    public function update(Request $request,string $cryptedId)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|max:20',
            'golongan' => 'required|string|max:10',
            'jabatan' => 'required|string|max:100',
            'unit' => 'required|string|max:100',
            'instansi' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
        ]);

        try {
            $id = Crypt::decrypt($cryptedId);
        
            $this->pesertaRepository->update($validatedData, $id);
        }catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('admin.peserta.index')->with('success', 'Data Peserta berhasil diperbarui');
    }

    public function destroy(string $cryptedId)
    {
        $id = Crypt::decrypt($cryptedId);
        
        $peserta = $this->pesertaRepository->find($id);

        $this->pesertaRepository->delete($id);
        $this->userRepository->delete($peserta->user_id);

        return redirect()->route('admin.peserta.index')->with('success', 'Data Peserta berhasil dihapus');
    }   
}
