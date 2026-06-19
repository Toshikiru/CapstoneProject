<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreExamRequest;
use App\Models\Exam;
use App\Models\ScoreInterpretation;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExamController extends Controller
{
    public function index(): View
    {
        $exams = Exam::with(['creator', 'examSessions'])
            ->withCount('questions')
            ->latest()
            ->paginate(15);

        return view('admin.exams.index', compact('exams'));
    }

    public function create(): View
    {
        return view('admin.exams.create');
    }

    public function store(StoreExamRequest $request): RedirectResponse
    {
        $exam = Exam::create([
            ...$request->validated(),
            'created_by'  => auth()->id(),
            'access_code' => Exam::generateAccessCode(),
        ]);

        // Default score interpretations
        ScoreInterpretation::insert([
            ['exam_id' => $exam->id, 'min_score' => 90, 'max_score' => 100, 'label' => 'Passed',       'admission_status' => 'Passed',      'created_at' => now(), 'updated_at' => now()],
            ['exam_id' => $exam->id, 'min_score' => 75, 'max_score' => 89,  'label' => 'Conditional',  'admission_status' => 'Conditional', 'created_at' => now(), 'updated_at' => now()],
            ['exam_id' => $exam->id, 'min_score' => 0,  'max_score' => 74,  'label' => 'Failed',       'admission_status' => 'Failed',      'created_at' => now(), 'updated_at' => now()],
        ]);

        ActivityLogService::log('exam_created', "Created exam: {$exam->title}", Exam::class, $exam->id);

        return redirect()->route('admin.exams.show', $exam)->with('success', 'Exam created successfully. You can now add questions.');
    }

    public function show(Exam $exam): View
    {
        $exam->load(['sections.questions', 'scoreInterpretations', 'examSessions.user']);
        return view('admin.exams.show', compact('exam'));
    }

    public function edit(Exam $exam): View
    {
        $exam->load('scoreInterpretations');
        return view('admin.exams.edit', compact('exam'));
    }

    public function update(StoreExamRequest $request, Exam $exam): RedirectResponse
    {
        $exam->update($request->validated());
        ActivityLogService::log('exam_updated', "Updated exam: {$exam->title}", Exam::class, $exam->id);

        return redirect()->route('admin.exams.show', $exam)->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam): RedirectResponse
    {
        $title = $exam->title;
        $exam->delete();
        ActivityLogService::log('exam_deleted', "Deleted exam: {$title}", Exam::class, $exam->id);

        return redirect()->route('admin.exams.index')->with('success', 'Exam deleted successfully.');
    }

    public function toggleActive(Exam $exam): RedirectResponse
    {
        $exam->update(['is_active' => ! $exam->is_active]);
        $status = $exam->is_active ? 'activated' : 'deactivated';
        ActivityLogService::log("exam_{$status}", "Exam {$status}: {$exam->title}", Exam::class, $exam->id);

        return back()->with('success', "Exam {$status} successfully.");
    }

    public function duplicate(Exam $exam): RedirectResponse
    {
        DB::transaction(function () use ($exam) {
            $newExam = $exam->replicate();
            $newExam->title       = $exam->title . ' (Copy)';
            $newExam->access_code = Exam::generateAccessCode();
            $newExam->is_active   = false;
            $newExam->save();

            foreach ($exam->sections as $section) {
                $newSection = $section->replicate();
                $newSection->exam_id = $newExam->id;
                $newSection->save();

                foreach ($section->questions as $question) {
                    $newQ = $question->replicate();
                    $newQ->exam_id    = $newExam->id;
                    $newQ->section_id = $newSection->id;
                    $newQ->save();
                }
            }

            foreach ($exam->scoreInterpretations as $interp) {
                $newInterp = $interp->replicate();
                $newInterp->exam_id = $newExam->id;
                $newInterp->save();
            }

            ActivityLogService::log('exam_duplicated', "Duplicated exam: {$exam->title}", Exam::class, $newExam->id);
        });

        return redirect()->route('admin.exams.index')->with('success', 'Exam duplicated successfully.');
    }

    public function regenerateCode(Exam $exam): RedirectResponse
    {
        $exam->update(['access_code' => Exam::generateAccessCode()]);
        return back()->with('success', 'New access code generated.');
    }

    /**
     * Update score interpretation thresholds for this exam.
     */
    public function updateInterpretations(Request $request, Exam $exam): RedirectResponse
    {
        $request->validate([
            'interpretations'                    => ['required', 'array', 'min:1'],
            'interpretations.*.id'                => ['required', 'exists:score_interpretations,id'],
            'interpretations.*.label'             => ['required', 'string', 'max:100'],
            'interpretations.*.min_score'         => ['required', 'numeric', 'min:0', 'max:100'],
            'interpretations.*.max_score'         => ['required', 'numeric', 'min:0', 'max:100', 'gte:interpretations.*.min_score'],
            'interpretations.*.admission_status'  => ['required', 'in:Passed,Conditional,Failed'],
        ]);

        // Validate ranges don't overlap and belong to this exam before saving.
        $rows = collect($request->interpretations)
            ->sortBy('min_score')
            ->values();

        for ($i = 0; $i < $rows->count() - 1; $i++) {
            if ((float) $rows[$i]['max_score'] >= (float) $rows[$i + 1]['min_score']) {
                return back()->withErrors([
                    'interpretations' => 'Score ranges cannot overlap. Please review the min/max values.',
                ])->withInput();
            }
        }

        foreach ($request->interpretations as $row) {
            $interp = ScoreInterpretation::where('id', $row['id'])
                ->where('exam_id', $exam->id)
                ->first();

            if (! $interp) {
                continue; // Skip rows that don't belong to this exam.
            }

            $interp->update([
                'label'            => $row['label'],
                'min_score'        => $row['min_score'],
                'max_score'        => $row['max_score'],
                'admission_status' => $row['admission_status'],
            ]);
        }

        ActivityLogService::log('score_interpretation_updated', "Updated score interpretation for exam: {$exam->title}", Exam::class, $exam->id);

        return redirect()->route('admin.exams.show', $exam)->with('success', 'Score interpretation updated successfully.');
    }
}
