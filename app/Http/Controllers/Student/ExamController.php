<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Response;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use App\Services\ScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function __construct(private ScoringService $scoring) {}

    /**
     * Show the access code entry form.
     */
    public function enterCode(): View
    {
        $user = auth()->user();
        $activeSession = ExamSession::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->with('exam')
            ->first();

        return view('student.exam.enter-code', compact('activeSession'));
    }

    /**
     * Validate access code and start or resume exam.
     */
    public function start(Request $request): RedirectResponse
    {
        $request->validate(['access_code' => ['required', 'string']]);

        $exam = Exam::where('access_code', strtoupper($request->access_code))->first();

        if (! $exam || ! $exam->isAvailable()) {
            return back()->withErrors(['access_code' => 'Invalid or unavailable access code.']);
        }

        $user = auth()->user();

        // Check for existing session
        $session = ExamSession::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->first();

        if ($session) {
            if ($session->status === 'in_progress') {
                // Resume existing session
                return redirect()->route('student.exam.take', $session);
            }
            // Already submitted
            return back()->withErrors(['access_code' => 'You have already completed this exam.']);
        }

        // Check max attempts
        $attempts = ExamSession::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->count();

        if ($attempts >= $exam->max_attempts) {
            return back()->withErrors(['access_code' => 'You have reached the maximum number of attempts for this exam.']);
        }

        // Create new session
        $session = ExamSession::create([
            'user_id'       => $user->id,
            'exam_id'       => $exam->id,
            'session_token' => ExamSession::generateToken(),
            'status'        => 'in_progress',
            'started_at'    => now(),
            'expires_at'    => now()->addMinutes($exam->time_limit),
            'time_remaining'=> $exam->time_limit * 60,
            'ip_address'    => $request->ip(),
            'browser_info'  => $request->userAgent(),
        ]);

        ActivityLogService::log('exam_started', "Started exam: {$exam->title}", ExamSession::class, $session->id);

        return redirect()->route('student.exam.take', $session);
    }

    /**
     * Show the exam taking page.
     */
    public function take(ExamSession $session): View|RedirectResponse
    {
        $this->authorizeSession($session);

        if ($session->isExpired() && $session->status === 'in_progress') {
            $this->autoSubmit($session);
            return redirect()->route('student.exam.result', $session)->with('info', 'Your exam was automatically submitted when time expired.');
        }

        if ($session->status !== 'in_progress') {
            return redirect()->route('student.exam.result', $session);
        }

        $session->load(['exam.sections.questions', 'responses']);

        $answeredIds = $session->responses->pluck('question_id')->toArray();

        return view('student.exam.take', compact('session', 'answeredIds'));
    }

    /**
     * AJAX: Auto-save answers every 10 seconds.
     */
    public function autoSave(Request $request, ExamSession $session): JsonResponse
    {
        $this->authorizeSession($session);

        if ($session->status !== 'in_progress') {
            return response()->json(['status' => 'expired'], 410);
        }

        // Allow a short grace window past the server expiry so the final
        // "flush before submit" call (triggered the instant the client
        // timer hits zero) can still persist the last few answers, rather
        // than racing the server clock and silently losing them.
        $graceSeconds = 15;
        if (now()->subSeconds($graceSeconds)->gt($session->expires_at)) {
            return response()->json(['status' => 'expired'], 410);
        }

        $request->validate([
            'answers'        => ['required', 'array'],
            'answers.*.id'   => ['required', 'exists:questions,id'],
            'answers.*.value'=> ['nullable', 'string'],
            'time_remaining' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($request->answers as $answer) {
            Response::updateOrCreate(
                ['exam_session_id' => $session->id, 'question_id' => $answer['id']],
                ['answer' => $answer['value']]
            );
        }

        $session->update(['time_remaining' => $request->time_remaining]);

        return response()->json([
            'status'         => 'saved',
            'time_remaining' => $session->time_remaining_in_seconds,
        ]);
    }

    /**
     * AJAX: Sync server time remaining.
     */
    public function syncTimer(ExamSession $session): JsonResponse
    {
        $this->authorizeSession($session);

        if ($session->isExpired()) {
            $this->autoSubmit($session);
            return response()->json(['status' => 'expired', 'time_remaining' => 0]);
        }

        return response()->json([
            'status'         => 'ok',
            'time_remaining' => $session->time_remaining_in_seconds,
        ]);
    }

    /**
     * AJAX: Track focus loss events.
     */
    public function focusLost(ExamSession $session): JsonResponse
    {
        $this->authorizeSession($session);
        $session->increment('focus_loss_count');

        return response()->json([
            'focus_loss_count' => $session->focus_loss_count,
            'warning'          => $session->focus_loss_count >= 3,
        ]);
    }

    /**
     * Final submission.
     */
    public function submit(Request $request, ExamSession $session): RedirectResponse
    {
        $this->authorizeSession($session);

        if ($session->status !== 'in_progress') {
            return redirect()->route('student.exam.result', $session);
        }

        // Save any remaining answers
        if ($request->has('answers')) {
            foreach ($request->answers as $qId => $answer) {
                Response::updateOrCreate(
                    ['exam_session_id' => $session->id, 'question_id' => $qId],
                    ['answer' => $answer]
                );
            }
        }

        $session->update([
            'status'       => 'submitted',
            'submitted_at' => now(),
        ]);

        // Auto-score
        $this->scoring->gradeSession($session);

        // Update student profile admission status
        $session->refresh();
        $session->user->studentProfile?->update([
            'admission_status' => $session->result_status,
        ]);

        // Notify admins
        NotificationService::notifyAdmins(
            'exam_submitted',
            'New Exam Submission',
            "{$session->user->name} has submitted the exam: {$session->exam->title}",
            ['session_id' => $session->id]
        );

        ActivityLogService::log('exam_submitted', "Submitted exam: {$session->exam->title}", ExamSession::class, $session->id);

        return redirect()->route('student.exam.result', $session)->with('success', 'Exam submitted successfully!');
    }

    /**
     * Show exam result.
     */
    public function result(ExamSession $session): View
    {
        $this->authorizeSession($session);
        $session->load(['exam', 'responses.question']);

        return view('student.exam.result', compact('session'));
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────────

    private function authorizeSession(ExamSession $session): void
    {
        if ($session->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }
    }

    private function autoSubmit(ExamSession $session): void
    {
        $session->update(['status' => 'timed_out', 'submitted_at' => now()]);
        $this->scoring->gradeSession($session);
        $session->refresh();
        $session->user->studentProfile?->update(['admission_status' => $session->result_status]);

        NotificationService::notifyAdmins(
            'exam_timed_out',
            'Exam Timed Out',
            "{$session->user->name}'s exam timed out: {$session->exam->title}",
            ['session_id' => $session->id]
        );
    }
}
