<?php

namespace App\Http\Controllers;

use App\Enums\RoleUserEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
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
}
