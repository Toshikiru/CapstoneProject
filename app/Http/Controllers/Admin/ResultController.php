<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Response;
use App\Services\ActivityLogService;
use App\Services\ScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function __construct(private ScoringService $scoring) {}

    public function index(Request $request): View
    {
        $query = ExamSession::with(['user.studentProfile', 'exam'])
            ->where('status', 'submitted');

        if ($examId = $request->get('exam_id')) {
            $query->where('exam_id', $examId);
        }

        if ($status = $request->get('result_status')) {
            $query->where('result_status', $status);
        }

        if ($search = $request->get('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('student_id', 'like', "%{$search}%"));
        }

        $sessions = $query->latest('submitted_at')->paginate(20)->withQueryString();
        $exams    = Exam::orderBy('title')->get();

        return view('admin.results.index', compact('sessions', 'exams'));
    }

    public function show(ExamSession $session): View
    {
        $session->load(['user.studentProfile', 'exam', 'responses.question']);
        return view('admin.results.show', compact('session'));
    }

    public function grade(Request $request, ExamSession $session): RedirectResponse
    {
        $request->validate([
            'responses'           => ['required', 'array'],
            'responses.*.id'      => ['required', 'exists:responses,id'],
            'responses.*.score'   => ['required', 'numeric', 'min:0'],
            'responses.*.remarks' => ['nullable', 'string'],
        ]);

        foreach ($request->responses as $data) {
            Response::where('id', $data['id'])->update([
                'score'              => $data['score'],
                'grader_remarks'     => $data['remarks'] ?? null,
                'is_manually_graded' => true,
                'graded_by'          => auth()->id(),
                'graded_at'          => now(),
            ]);
        }

        $this->scoring->calculateTotals($session);

        // Update student profile admission status
        $session->user->studentProfile?->update([
            'admission_status' => $session->result_status,
        ]);

        ActivityLogService::log('manual_grading', "Manually graded session ID: {$session->id}", ExamSession::class, $session->id);

        return redirect()->route('admin.results.show', $session)->with('success', 'Grading saved and scores recalculated.');
    }

    public function admissionSlip(ExamSession $session): View
    {
        $session->load(['user.studentProfile', 'exam']);
        return view('admin.results.admission-slip', compact('session'));
    }
}
