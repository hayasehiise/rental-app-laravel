<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }
        if (auth()->check() && !auth()->user()->hasAnyRole([
            'admin',
            'staff_admin',
            'finance_admin',
        ])) {
            auth()->logout();

            return redirect()->route('filament.admin.auth.login')->withErrors(['email' => 'Anda tidak memiliki akses ke Admin Panel']);
        }
        return $next($request);
    }
}
