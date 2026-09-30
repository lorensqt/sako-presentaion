<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class EnsureMemberAccess
{
    /**
     * Handle an incoming request.
     * Restricts internal staff administrators from accessing member portal views.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/');
        }

        // Isolate staff administrators from the member portal
        if ($user->role === 'admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Administrative staff accounts are restricted to the administration console and cannot access member self-service portal.',
                ], 403);
            }

            $fallbackRoute = $user->firstAccessibleAdminRoute();
            $target = ($fallbackRoute && Route::has($fallbackRoute)) ? route($fallbackRoute) : url('/admin/dashboard');

            return redirect($target)->withErrors([
                'login_identifier' => 'Administrative staff accounts are restricted to the administration panel and cannot access the member portal.',
            ]);
        }

        return $next($request);
    }
}
