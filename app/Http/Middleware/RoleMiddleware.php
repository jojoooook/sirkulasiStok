<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles = null)
    {
        $user = Auth::user();

        Log::info('RoleMiddleware: Authenticated user role: ' . ($user ? $user->role : 'null'));

        // If no user is authenticated, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

        // If the user is an admin, allow access to all routes
        if ($user->role === 'admin') {
            return $next($request);  // Admin can access everything
        }

        // If role(s) is passed and user role matches one of them, allow access
        if ($roles) {
            $rolesArray = explode(',', $roles);
            if (in_array($user->role, $rolesArray)) {
                return $next($request);  // Role matches, continue
            }

            // If role doesn't match, flash a custom error message and redirect
            session()->flash('error', 'Anda tidak memiliki akses ke halaman ini.');
            return redirect()->route('dashboard');  // Redirect to homepage
        }

        // Deny access if role is neither admin nor the specified role
        abort(403, 'Unauthorized action.');
    }
}
