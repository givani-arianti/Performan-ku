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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $activeRole = session('active_role', $user->role);
        
        // Map hr to admin in checking
        $effectiveRole = $activeRole === 'hr' ? 'admin' : $activeRole;
        
        if (in_array($effectiveRole, $roles) || ($user->role === 'admin' && in_array('manajer', $roles) && $activeRole === 'manajer')) {
            return $next($request);
        }

        // Redirect based on role if they try to access something they shouldn't
        if ($activeRole === 'karyawan') {
            return redirect()->route('karyawan.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        } elseif ($activeRole === 'admin' || $activeRole === 'hr') {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        } elseif ($activeRole === 'manajer') {
            return redirect()->route('manajer.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return redirect('/')->with('error', 'Akses ditolak.');
    }
}
