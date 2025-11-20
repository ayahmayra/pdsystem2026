<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUnitScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Super admin dan admin dapat mengakses semua data
        if ($user->hasRole(['super-admin', 'admin', 'bendahara-pengeluaran'])) {
            return $next($request);
        }

        // Bendahara pengeluaran pembantu dan sekretariat dibatasi berdasarkan unit
        if ($user->hasRole(['bendahara-pengeluaran-pembantu', 'sekretariat'])) {
            // Tambahkan unit_id ke request untuk filtering di controller
            $request->merge(['user_unit_id' => $user->unit_id]);
        }

        return $next($request);
    }
}