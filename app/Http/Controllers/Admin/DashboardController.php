<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_students'   => User::where('role', 'student')->count(),
            'total_examinees'  => ExamSession::distinct('user_id')->count(),
            'passed'           => ExamSession::where('result_status', 'Passed')->count(),
            'conditional'      => ExamSession::where('result_status', 'Conditional')->count(),
            'failed'           => ExamSession::where('result_status', 'Failed')->count(),
            'pending_grading'  => ExamSession::where('is_graded', false)->where('status', 'submitted')->count(),
            'active_exams'     => Exam::where('is_active', true)->count(),
            'in_progress'      => ExamSession::where('status', 'in_progress')->count(),
        ];

        $courseDistribution = StudentProfile::selectRaw('course, count(*) as count')
            ->groupBy('course')
            ->orderByDesc('count')
            ->get();

        $yearDistribution = StudentProfile::selectRaw('year_level, count(*) as count')
            ->groupBy('year_level')
            ->orderBy('year_level')
            ->get();

        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        // Daily exam submissions for the last 14 days, for the activity chart.
        $dailyActivity = ExamSession::where('submitted_at', '>=', now()->subDays(13)->startOfDay())
            ->whereNotNull('submitted_at')
            ->selectRaw('DATE(submitted_at) as day, count(*) as count')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $dailyLabels = [];
        $dailyCounts = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dailyLabels[] = now()->subDays($i)->format('M d');
            $dailyCounts[] = $dailyActivity->get($date)?->count ?? 0;
        }

        return view('admin.dashboard.index', compact(
            'stats', 'courseDistribution', 'yearDistribution', 'recentActivities', 'dailyLabels', 'dailyCounts'
        ));
    }
}
