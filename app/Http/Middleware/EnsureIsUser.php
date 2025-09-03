<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        if (!auth()->check()) {
            return redirect()->route('login')->withErrors(['auth' => 'Anda Harus Login untuk Melanjutkan']);
        }

        if (!auth()->user()->hasAnyRole(['guest', 'member'])) {
            auth()->logout();

            return redirect()->route('login')->withErrors(['auth' => 'Role Anda tidak diizinkan mengakses area user']);
        }
        return $next($request);
    }
}
