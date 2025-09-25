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

        if (!Auth::attempt($validated)) {
            $user = Auth::user();

            if (!$user->verify->verified_status) {
                Auth::logout();
                throw new Exception('Akun belum terverifikasi');
            }

            if (!$user->hasAnyRole(['guest', 'member'])) {
                Auth::logout();
                throw new Exception('Akun bukan untuk user login');
            }

            return false;
        }

        $request->session()->regenerate();

        return true;
    }
}
