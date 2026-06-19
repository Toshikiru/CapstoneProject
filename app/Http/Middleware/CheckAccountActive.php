<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            if (! $user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['student_id' => 'Your account has been deactivated. Please contact the Guidance Office.']);
            }

            if ($user->isLocked()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $minutes = (int) ceil(($user->locked_until->getTimestamp() - now()->getTimestamp()) / 60);
                return redirect()->route('login')
                    ->withErrors(['student_id' => "Your account is temporarily locked. Please try again in {$minutes} minute(s)."]);
            }
        }

        return $next($request);
    }
}
