<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login.user')->withErrors(['auth' => 'Anda Harus Login untuk Melanjutkan']);
        } else if ($user && !$user->hasAnyRole(['member', 'guest'])) {
            auth()->logout();
            session()->flush();
            return redirect()->route('login.user')->withErrors(['auth' => 'Role Anda tidak diizinkan mengakses area user']);
        }

        return $next($request);
    }
}
