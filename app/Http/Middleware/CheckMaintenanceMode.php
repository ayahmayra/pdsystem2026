<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\OrgSettings;

class CheckMaintenanceMode
{
    /**
     * Routes that should be excluded from maintenance mode check
     */
    protected $except = [
        'login',
        'logout',
        'register',
        'password/*',
        'forgot-password',
        'reset-password',
        'verify-email',
        'email/*',
        'confirm-password',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Always allow authentication routes
        if ($this->inExceptArray($request)) {
            return $next($request);
        }

        // Get org settings
        $orgSettings = OrgSettings::getInstance();
        
        // Check if maintenance mode is enabled
        if ($orgSettings->maintenance_mode) {
            // Check if user is authenticated
            if (auth()->check()) {
                $user = auth()->user();
                
                // Allow superadmin to bypass maintenance mode
                // Check both 'superadmin' and 'super-admin' for compatibility
                if ($user->hasRole('superadmin') || $user->hasRole('super-admin')) {
                    // Add header to indicate superadmin bypass (for debugging)
                    $response = $next($request);
                    if (method_exists($response, 'header')) {
                        $response->header('X-Maintenance-Bypass', 'superadmin');
                    }
                    return $response;
                }
            }
            
            // Show maintenance page for all other users
            return response()->view('maintenance', [
                'message' => $orgSettings->maintenance_message ?? 'Sistem sedang dalam perbaikan. Silakan coba lagi nanti.'
            ], 503);
        }
        
        return $next($request);
    }

    /**
     * Determine if the request has a URI that should pass through maintenance check.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except) || $request->fullUrlIs($except)) {
                return true;
            }
        }

        return false;
    }
}
