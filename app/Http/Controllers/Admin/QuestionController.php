<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuestionRequest;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Section;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionController extends Controller
{
    public function index(Exam $exam): View
    {
        $exam->load('sections.questions');
        return view('admin.questions.index', compact('exam'));
    }

    public function storeSection(Request $request, Exam $exam): RedirectResponse
    {
        $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
        ]);

        $section = $exam->sections()->create([
            'title'        => $request->title,
            'description'  => $request->description,
            'instructions' => $request->instructions,
            'order'        => $exam->sections()->max('order') + 1,
        ]);

        ActivityLogService::log('section_created', "Added section: {$section->title} to exam: {$exam->title}");

        return back()->with('success', 'Section added successfully.');
    }

    public function store(StoreQuestionRequest $request, Exam $exam): RedirectResponse
    {
        $options = match (true) {
            $request->type === 'multiple_choice' => array_values(array_filter($request->options ?? [])),
            $request->type === 'true_or_false'   => ['True', 'False'],
            $request->type === 'likert_scale'    => [
                '1 - Strongly Disagree',
                '2 - Disagree',
                '3 - Neutral',
                '4 - Agree',
                '5 - Strongly Agree',
            ],
            default => null,
        };

        $question = $exam->questions()->create([
            'section_id'    => $request->section_id,
            'type'          => $request->type,
            'question_text' => $request->question_text,
            'options'       => $options,
            'correct_answer'=> $request->correct_answer,
            'points'        => $request->points,
            'order'         => $exam->questions()->max('order') + 1,
        ]);

        ActivityLogService::log('question_added', "Added question to exam: {$exam->title}", Question::class, $question->id);

        return back()->with('success', 'Question added successfully.');
    }

    public function update(Request $request, Exam $exam, Question $question): RedirectResponse
    {
        $request->validate([
            'question_text'  => ['required', 'string'],
            'correct_answer' => ['nullable', 'string'],
            'points'         => ['required', 'numeric', 'min:0'],
        ]);

        $question->update($request->only(['question_text', 'correct_answer', 'points', 'options']));
        ActivityLogService::log('question_updated', "Updated question ID: {$question->id}");

        return back()->with('success', 'Question updated successfully.');
    }

    public function destroy(Exam $exam, Question $question): RedirectResponse
    {
        $question->delete();
        ActivityLogService::log('question_deleted', "Deleted question from exam: {$exam->title}");

        return back()->with('success', 'Question deleted successfully.');
    }

    public function reorder(Request $request, Exam $exam): JsonResponse
    {
        $request->validate(['order' => ['required', 'array']]);

        foreach ($request->order as $index => $id) {
            Question::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
