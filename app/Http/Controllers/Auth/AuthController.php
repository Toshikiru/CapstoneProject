<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $user = User::where('student_id', $request->student_id)
            ->whereNull('deleted_at')
            ->first();

        // User not found
        if (! $user) {
            return back()->withErrors(['student_id' => 'Invalid Student ID or password.'])->onlyInput('student_id');
        }

        // Account locked?
        if ($user->isLocked()) {
            $minutes = (int) ceil(($user->locked_until->getTimestamp() - now()->getTimestamp()) / 60);
            ActivityLogService::log('login_blocked', "Locked account login attempt for: {$user->student_id}");
            return back()->withErrors(['student_id' => "Account locked. Try again in {$minutes} minute(s)."])->onlyInput('student_id');
        }

        // Account inactive?
        if (! $user->is_active) {
            return back()->withErrors(['student_id' => 'Your account has been deactivated. Contact the Guidance Office.'])->onlyInput('student_id');
        }

        // Attempt authentication
        if (! Auth::attempt(['student_id' => $request->student_id, 'password' => $request->password], $request->boolean('remember'))) {
            $user->incrementFailedAttempts();
            ActivityLogService::log('login_failed', "Failed login attempt for: {$request->student_id}");
            return back()->withErrors(['student_id' => 'Invalid Student ID or password.'])->onlyInput('student_id');
        }

        // Success
        $user->resetFailedAttempts();
        $request->session()->regenerate();

        ActivityLogService::log('login', "User logged in: {$user->name}", User::class, $user->id);

        return $this->redirectByRole($user);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        ActivityLogService::log('logout', "User logged out: {$user->name}", User::class, $user->id);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been logged out successfully.');
    }

    /**
     * Redirect user to appropriate dashboard by role.
     */
    private function redirectByRole(User $user): RedirectResponse
    {
        return match ($user->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default   => redirect()->route('login'),
        };
    }
}
