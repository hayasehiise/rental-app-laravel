<?php

namespace App\Actions\Auth;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\Concerns\AsAction;

class SendPasswordResetLink
{
    use AsAction;

    public function handle(string $email): void
    {
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        $url = url(route('password.reset', [
            'token' => $token,
            'email' => urlencode($email),
        ], false));

        Mail::to($email)->send(new ResetPasswordMail($url));
    }
}
