<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Repositories\AdminRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class AdminProfilController extends Controller
{
    protected AdminRepository $adminRepository;
    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function index()
    {
        $admin = $this->adminRepository->getByUserId(Auth::id());
        return view('admin.profil', compact('admin'));
    }

    public function update(Request $request, int $id)
    {
        $validatedData = $request->validate([
            'nama'      => 'required|string|max:255',
            'nip'       => 'required|string|max:20',
            'unit'      => 'required|string|max:255',
            'instansi'  => 'required|string|max:255',
            'telepon'   => 'required|string|max:20',
            'email'     => 'required|email|max:255',
        ],[
            'nama.required'     => 'Nama wajib diisi.',
            'nip.required'      => 'NIP wajib diisi.',
            'unit.required'     => 'Unit wajib diisi.',
            'instansi.required' => 'Instansi wajib diisi.',
            'telepon.required'  => 'Telepon wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
        ]);

        $admin = $this->adminRepository->getById($id);

        if ($admin->email !== $validatedData['email'] || $admin->nip !== $validatedData['nip']) {
            $request->validate([
                'email' => 'unique:users,email,' . $admin->user_id,
                'nip'   => 'unique:users,nip,' . $admin->user_id,
            ]);

            $admin->user->update([
                'email' => $validatedData['email'],
                'nip'   => $validatedData['nip'],
            ]);
        }

        $admin->update($validatedData);

        return redirect()->route('admin.profil')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePhoto(Request $request, int $id)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'foto.required' => 'Pilih foto profil terlebih dahulu.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $admin = $this->adminRepository->getById($id);
        $oldPhoto = $admin->foto;
        $newPhoto = $request->file('foto')->store('admin/profil', 'public');

        if ($newPhoto === false) {
            throw new RuntimeException('Foto profil gagal disimpan.');
        }

        try {
            $admin->update(['foto' => $newPhoto]);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($newPhoto);
            throw $exception;
        }

        if ($oldPhoto) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return redirect()->route('admin.profil')->with('success', 'Foto profil berhasil diperbarui.');
    }
}
