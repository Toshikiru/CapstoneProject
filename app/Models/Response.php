<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Response extends Model
{
    protected $fillable = [
        'exam_session_id', 'question_id', 'answer', 'score',
        'is_correct', 'is_manually_graded', 'grader_remarks',
        'graded_by', 'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'is_correct'          => 'boolean',
            'is_manually_graded'  => 'boolean',
            'graded_at'           => 'datetime',
            'score'               => 'decimal:2',
        ];
    }

    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
