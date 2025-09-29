<?php

namespace App\Actions\Auth;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

class LoginUser
{
    use AsAction;

    public function handle(Request $request): bool
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6', 'string'],
        ]);

        // cek credentials
        if (!Auth::attempt($validated)) {
            return false;
        }

        $user = Auth::user();
        // cek verified
        if (!$user->verify->verified_status) {
            Auth::logout();
            throw new Exception('Akun belum terverifikasi');
        }

        // cek role
        if (!$user->hasAnyRole(['guest', 'member'])) {
            Auth::logout();
            throw new Exception('Akun bukan untuk user login');
        }

        $request->session()->regenerate();

        return true;
    }
}
