<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function index(Request $request): View
{
    $exams = Exam::where('is_active', true)
        ->with(['examSessions' => function ($q) {
            $q->whereIn('status', ['in_progress', 'submitted']);
        }])
        ->get();

    $examId = $request->get('exam_id', $exams->first()?->id);

    $sessions = ExamSession::with(['user:id,name,student_id', 'exam:id,title,time_limit'])
        ->whereIn('status', ['in_progress', 'submitted'])
        ->when($examId, fn ($q) => $q->where('exam_id', $examId))
        ->get()
        ->map(function ($session) {
            return [
                'id' => $session->id,
                'student_name' => $session->user->name ?? 'N/A',
                'student_id' => $session->user->student_id ?? 'N/A',
                'exam_title' => $session->exam->title ?? 'N/A',
                'status' => $session->status,
            ];
        });

    return view('admin.monitoring.index', compact('exams', 'examId', 'sessions'));
}

    /**
     * AJAX endpoint for live monitoring data — polled every 5 seconds.
     */
    public function live(Request $request): JsonResponse
    {
        $examId = $request->get('exam_id');

        $query = ExamSession::with(['user:id,name,student_id', 'exam:id,title,time_limit'])
            ->whereIn('status', ['in_progress', 'submitted']);

        if ($examId) {
            $query->where('exam_id', $examId);
        }

        $sessions = $query->get()->map(function ($session) {
            return [
                'id'             => $session->id,
                'student_name'   => $session->user->name,
                'student_id'     => $session->user->student_id,
                'exam_title'     => $session->exam->title,
                'status'         => $session->status,
                'started_at'     => $session->started_at->format('h:i A'),
                'time_remaining' => $session->time_remaining_in_seconds,
                'submitted_at'   => $session->submitted_at?->format('h:i A'),
            ];
        });

        return response()->json([
            'sessions'    => $sessions,
            'online'      => $sessions->where('status', 'in_progress')->count(),
            'submitted'   => $sessions->where('status', 'submitted')->count(),
            'timestamp'   => now()->format('h:i:s A'),
        ]);
    }

    /**
     * Force-submit or invalidate a session.
     */
    public function invalidate(ExamSession $session): JsonResponse
    {
        $session->update([
            'status'       => 'invalidated',
            'submitted_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Session invalidated.']);
    }
}
