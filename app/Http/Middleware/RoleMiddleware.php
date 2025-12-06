<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('register');
        }

        $user = auth()->user();
        
        if (!$user->role || $user->role !== $role) {
            // Redirect to a neutral, non-role-gated route to avoid redirect loops
            return redirect()->route('public.home')->with('error', 'Akses dialihkan sesuai peran Anda.');
        }

        // If tenant but no active (occupied) booking, redirect to seeker dashboard
        if ($role === 'tenant' && method_exists($user, 'hasActiveBooking') && !$user->hasActiveBooking()) {
            return redirect()->route('public.home')
                             ->with('error', 'Akses dashboard penghuni dibatasi. Silakan lakukan booking dan pindah ke kamar terlebih dahulu.');
        }

        return $next($request);
    }
}
