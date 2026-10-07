<?php

namespace App\Http\Controllers;

use App\Enums\RoleUserEnum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Repositories\PesertaRepository;
use App\Repositories\UserRepository;

class AuthController extends Controller
{
    protected PesertaRepository $pesertaRepository;
    protected UserRepository $userRepository;
    
    public function __construct(
        PesertaRepository $pesertaRepository,
        UserRepository $userRepository)
    {
        $this->pesertaRepository = $pesertaRepository;
        $this->userRepository = $userRepository;
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->role === RoleUserEnum::ADMIN->value) {
                return redirect()->intended('/jadwal-ujian');
            }elseif ($user->role === RoleUserEnum::PESERTA->value) {
                return redirect()->intended('/beranda');
            }else {
                return redirect()->intended('/dashboard-penguji');
            }
        
        }

        return back()->with('error', 'Email atau kata sandi salah.')->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect('/login');
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'nip'                   => 'required|string|max:255',
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
            ],
            'password_confirmation' => 'required|string',
            ],[
            'email.required'        => 'Alamat email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
            'email.unique'          => 'Email telah terdaftar.',
            'nip.required'          => 'NIP wajib diisi.',
            'name.required'         => 'Nama lengkap wajib diisi.',
            'password.required'     => 'Kata sandi wajib diisi.',
            'password.min'          => 'Kata sandi minimal berisi 8 karakter.',
            'password.regex'        => 'Kata sandi harus mengombinasikan minimal 1 huruf kapital dan 1 angka.',
        ]);
   
        try {
            $validatedData['role'] = RoleUserEnum::PESERTA->value;

            // Handling password
            if($validatedData['password'] !== $validatedData['password_confirmation']) {
                return back()->withInput()->withErrors([
                    'password' => 'Kata sandi dan konfirmasi kata sandi tidak cocok.'
                ]);
            }
            $validatedData['password'] = Hash::make($validatedData['password']);

            // Handling existing user
            $existingUser = $this->userRepository->getByNipAndName($validatedData['nip'], $validatedData['name']);
            if ($existingUser) {
                return back()->withInput()->withErrors([
                    'nip' => 'NIP dan nama telah terdaftar dalam sistem.'
                ]);
            }
            $user = User::create($validatedData);

            // Handling data peserta
            $dataPeserta = [
                'nama'      => $validatedData['name'],
                'nip'       => $validatedData['nip'],
                'email'     => $validatedData['email'],
                'user_id'   => $user->id
            ];
            $this->pesertaRepository->create($dataPeserta);
    
            Auth::login($user);
    
            return redirect()->intended('/beranda');
    
        } catch (\Throwable $e) {
            Log::error('Register Error: ' . $e->getMessage());
    
            return back()->withInput()->withErrors([
                'email' => 'Gagal membuat akun: ' . $e->getMessage()
            ]);
        }
    }
}
