<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\VerifyUserMail;
use App\Models\VerifyUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6', 'string']
        ]);

        if (Auth::attempt($validated)) {
            $user = Auth::user();

            if (!$user->verify->verified_status) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun belum terverifikasi'
                ]);
            }

            if (!$user->hasAnyRole(['member', 'guest'])) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun bukan untuk user login'
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Akun Tidak terdaftar'
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password'])
        ]);
        $user->assignRole('guest');

        $token = Str::random(64);
        VerifyUser::create([
            'user_id' => $user->id,
            'verified_status' => false,
            'verification_token' => $token,
            'verification_expire_at' => now()->addDay(),
        ]);

        Mail::to($user->email)->send(new VerifyUserMail($token));

        return redirect()->route('register.user')->with('success', 'Verifikasi Email Anda terlebih dahulu');
    }

    public function verify($token)
    {
        $verified = VerifyUser::where('verification_token', $token)
            ->where('verification_expire_at', '>', now())
            ->firstOrFail();

        $verified->update([
            'verified_status' => true,
            'verification_token' => null,
            'verification_expire_at' => null,
        ]);

        $user = $verified->user;
        Auth::login($user);

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
