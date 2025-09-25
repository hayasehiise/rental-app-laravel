<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginUser;
use App\Actions\Auth\RegisterUser;
use App\Actions\Auth\VerifyUser as AuthVerifyUser;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;

class FrontendAuth extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/login');
    }

    public function showRegister()
    {
        return Inertia::render('Auth/register');
    }

    public function login(Request $request, LoginUser $loginUser)
    {
        try {
            $loginUser->run($request);
            return redirect()->intended(route('home'));
        } catch (Exception $e) {
            return back()->withErrors(['email' => $e->getMessage()]);
        }
    }

    public function register(Request $request, RegisterUser $registerUser)
    {
        $registerUser->run($request->all());
        return redirect()->route('login.user')->with('success', 'Verifikasi Email Anda terlebih dahulu');
    }

    public function verify(string $token, AuthVerifyUser $verifyUser)
    {
        $verifyUser->run($token);
        return redirect()->route('home')->with('success', 'Akun Berhasil Diverifikasi');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
