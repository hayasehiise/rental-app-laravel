<?php

namespace App\Actions\Auth;

use App\Mail\VerifyUserMail;
use App\Models\User;
use App\Models\VerifyUser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class RegisterUser
{
    use AsAction;

    public function handle(array $data): User
    {
        $validated = validator($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ])->validate();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
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

        return $user;
    }
}
