<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
   public function index(): View
{
    $user = auth()->user()->load(['studentProfile', 'examSessions.exam']);

    $examSessions = $user->examSessions()->with('exam')->latest()->get();

    $recentSessions = $examSessions->take(10);

    $latestSession = $examSessions->first();

    $notifications = $user->unreadNotifications()
        ->latest()
        ->limit(5)
        ->get();

    $stats = [
        'exams_taken' => $examSessions->count(),
        'admission_status' => $latestSession->result_status ?? 'Pending',
        'best_score' => $examSessions->max('percentage'),
    ];

    return view('student.dashboard.index', compact(
        'user',
        'examSessions',
        'recentSessions',
        'latestSession',
        'notifications',
        'stats'
    ));
}
}
