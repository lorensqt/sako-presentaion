<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPageAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/');
        }

        // Must have administrative role
        if (!in_array($user->role, ['admin', 'super_admin'], true)) {
            abort(403, 'Unauthorized access.');
        }

        // Check if the user has the required admin page permission
        if (!$user->canAccessAdminPage($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Access denied. You do not have permission to access this administrative module.',
                ], 403);
            }

            $fallbackRoute = $user->firstAccessibleAdminRoute();
            if ($fallbackRoute && Route::has($fallbackRoute) && $request->route() && $request->route()->getName() !== $fallbackRoute) {
                return redirect()->route($fallbackRoute)->withErrors([
                    'login_identifier' => 'Access denied. You do not have permission to access that module.',
                ]);
            }

            abort(403, 'Access denied. You do not have permission to access this administrative module.');
        }

        return $next($request);
    }
}
