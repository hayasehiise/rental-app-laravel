<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FrontendAuth extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6', 'string']
        ]);

        if (Auth::attempt($validated)) {
            $user = Auth::user();

            if (!$user->hasAnyRole(['member', 'guest'])) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun bukan untuk user login'
                ]);
            } else if (!$user->verify->verified_status) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun belum terverifikasi'
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Akun Tidak terdaftar'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
