<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\ResetPassword;
use App\Actions\Auth\SendPasswordResetLink;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PasswordReset extends Controller
{
    public function requestForm()
    {
        return Inertia::render('Auth/forgotPassword');
    }

    public function sendResetLink(Request $request, SendPasswordResetLink $sendPasswordResetLink)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $sendPasswordResetLink->run($request->email);

        return back()->with('status', 'Link Reset password sudah dikirim ke email anda');
    }

    public function resetForm(Request $request)
    {
        return Inertia::render('Auth/resetPassword', [
            'token' => $request->query('token'),
            'email' => urldecode($request->query('email')),
        ]);
    }

    public function resetPassword(Request $request, ResetPassword $resetPassword)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'min:6', 'confirmed'],
            'token' => ['required'],
        ]);

        $resetPassword->run($request->email, $request->password, $request->token);

        return redirect()->route('login.user')->with('status', 'Password Berhasil Diubah');
    }
}
