<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * Laravel's built-in "guest" middleware redirects authenticated users to
     * a route literally named "dashboard" or "home" by default — neither of
     * which exists in this app (we have "admin.dashboard" and
     * "student.dashboard"). Without this override, an already-logged-in user
     * visiting /login hits a broken redirect target, which combined with the
     * root route bouncing back to /login produces an infinite redirect loop
     * (ERR_TOO_MANY_REDIRECTS) instead of a clean redirect to the right
     * dashboard.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                $role = $user !== null ? $user->role : null;

                return redirect()->to(match ($role) {
                    'admin'   => route('admin.dashboard'),
                    'student' => route('student.dashboard'),
                    default   => route('login'),
                });
            }
        }

        return $next($request);
    }
}
