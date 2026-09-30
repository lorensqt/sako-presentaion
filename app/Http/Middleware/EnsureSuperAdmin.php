<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/');
        }

        if ($user->role !== 'super_admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Access denied. Super Administrator privileges required.',
                ], 403);
            }

            if ($user->role === 'admin') {
                $fallbackRoute = $user->firstAccessibleAdminRoute();
                if ($fallbackRoute && Route::has($fallbackRoute)) {
                    return redirect()->route($fallbackRoute)->withErrors([
                        'access' => 'Security Notice: The requested section is restricted strictly to Super Administrators.',
                    ]);
                }
            }

            abort(403, 'Access denied. Super Administrator privileges required.');
        }

        return $next($request);
    }
}
