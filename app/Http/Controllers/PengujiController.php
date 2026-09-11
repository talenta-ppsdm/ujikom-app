<?php

namespace App\Http\Controllers;

use App\Enums\RoleUserEnum;
use App\Repositories\PengujiRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class PengujiController extends Controller
{
    protected PengujiRepository $pengujiRepository;
    protected UserRepository $userRepository;

    public function __construct(
        PengujiRepository $pengujiRepository,
        UserRepository $userRepository,
    )
    {
        $this->pengujiRepository = $pengujiRepository;
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $penguji = $this->pengujiRepository->all();
        return view('penguji', compact('penguji'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|max:20|unique:penguji,nip',
            'golongan' => 'required|string|max:10',
            'jabatan' => 'required|string|max:100',
            'unit' => 'required|string|max:100',
            'instansi' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'bidang_keahlian' => 'required|string|max:255',
        ]);

        // Create user data for the penguji
        $userData = [
            'name' => $validatedData['nama'],
            'email' => $validatedData['email'],
            'password' => bcrypt('peserta2026'), 
            'nip' => $validatedData['nip'],
            'role' => RoleUserEnum::PENGUJI->value,
        ];
        $user = $this->userRepository->create($userData);

        // Create peserta by add userId
        $this->pengujiRepository->create(array_merge(
            $validatedData, ['user_id' => $user->id])
        );

        return redirect()->route('penguji.index')->with('success', 'Data Penguji berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|max:20',
            'golongan' => 'required|string|max:10',
            'jabatan' => 'required|string|max:100',
            'unit' => 'required|string|max:100',
            'instansi' => 'required|string|max:100',
            'telepon' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'bidang_keahlian' => 'required|string|max:255',
        ]);

        $this->pengujiRepository->update($validatedData, $id);

        return redirect()->route('penguji.index')->with('success', 'Data Penguji berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $this->pengujiRepository->delete($id);

        return redirect()->route('penguji.index')->with('success', 'Data Penguji berhasil dihapus.');
    }
}
