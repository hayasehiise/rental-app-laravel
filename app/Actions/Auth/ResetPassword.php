<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class ResetPassword
{
    use AsAction;

    public function handle(string $email, string $password, string $token): void
    {
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record || !Hash::check($token, $record->token)) {
            throw ValidationException::withMessages([
                'token' => 'Token Reset Password Tidak Valid',
            ]);
        }

        $user = User::where('email', $email)->firstOrFail();
        $user->update([
            'password' => Hash::make($password),
        ]);

        DB::table('password_reset_tokens')->where('email', $email)->delete();
    }
}
