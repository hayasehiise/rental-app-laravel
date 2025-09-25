<?php

namespace App\Actions\Auth;

use App\Models\VerifyUser as ModelsVerifyUser;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

class VerifyUser
{
    use AsAction;

    public function handle(string $token)
    {
        $verified = ModelsVerifyUser::where('verification_token', $token)
            ->where('verification_expire_at', '>', now())
            ->firstOrFail();

        $verified->update([
            'verified_status' => true,
            'verification_token' => null,
            'verification_expire_at' => null,
        ]);

        Auth::login($verified->user);

        return $verified->user;
    }
}
