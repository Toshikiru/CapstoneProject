<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\Response;
use App\Models\ScoreInterpretation;

class ScoringService
{
    /**
     * Auto-grade all auto-gradeable responses for a session.
     */
    public function gradeSession(ExamSession $session): void
    {
        $responses = $session->responses()->with('question')->get();

        foreach ($responses as $response) {
            $question = $response->question;

            if ($question->requiresManualGrading()) {
                continue;
            }

            $this->gradeResponse($response, $question);
        }

        $this->calculateTotals($session);
    }

    /**
     * Grade a single auto-gradeable response.
     */
    public function gradeResponse(Response $response, Question $question): void
    {
        $isCorrect = false;
        $score     = 0;

        switch ($question->type) {
            case 'multiple_choice':
            case 'true_or_false':
                $isCorrect = strtolower(trim($response->answer ?? ''))
                    === strtolower(trim($question->correct_answer ?? ''));
                $score     = $isCorrect ? $question->points : 0;
                break;

            case 'likert_scale':
                // Likert scale: scale the 1–5 selection proportionally against
                // the question's configured point value (5 is the max scale value).
                $selected  = (float) ($response->answer ?? 0);
                $score     = round(($selected / 5) * (float) $question->points, 2);
                $isCorrect = null; // No right or wrong for Likert
                break;

            default:
                // Unknown question type — leave unscored rather than silently zeroing,
                // so it surfaces during grading review instead of being lost.
                $isCorrect = null;
                $score     = null;
                break;
        }

        $response->update([
            'is_correct' => $isCorrect,
            'score'      => $score,
        ]);
    }

    /**
     * Recalculate session totals and set interpretation/admission status.
     */
    public function calculateTotals(ExamSession $session): void
    {
        $session->load(['responses', 'exam.scoreInterpretations']);

        $rawScore = $session->responses()->sum('score');
        $total    = $session->exam->questions()->sum('points');
        $percentage = $total > 0 ? ($rawScore / $total) * 100 : 0;

        $interpretation = $this->getInterpretation($session->exam_id, $percentage);

        $session->update([
            'raw_score'     => $rawScore,
            'percentage'    => round($percentage, 2),
            'interpretation'=> $interpretation['label'] ?? 'Pending',
            'result_status' => $interpretation['admission_status'] ?? 'Pending',
            'is_graded'     => ! $session->responses()
                                    ->whereHas('question', fn($q) => $q->where('type', 'short_answer'))
                                    ->whereNull('score')
                                    ->exists(),
        ]);
    }

    /**
     * Look up score interpretation for a given percentage.
     */
    private function getInterpretation(int $examId, float $percentage): array
    {
        $interp = ScoreInterpretation::where('exam_id', $examId)
            ->where('min_score', '<=', $percentage)
            ->where('max_score', '>=', $percentage)
            ->first();

        if ($interp) {
            return [
                'label'            => $interp->label,
                'admission_status' => $interp->admission_status,
            ];
        }

        // Fallback defaults
        if ($percentage >= 90) {
            return ['label' => 'Passed', 'admission_status' => 'Passed'];
        } elseif ($percentage >= 75) {
            return ['label' => 'Conditional', 'admission_status' => 'Conditional'];
        } else {
            return ['label' => 'Failed', 'admission_status' => 'Failed'];
        }
    }
}
