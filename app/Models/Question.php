<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'section_id', 'exam_id', 'type', 'question_text',
        'options', 'correct_answer', 'points', 'order', 'is_required',
    ];

    protected function casts(): array
    {
        return [
            'options'     => 'array',
            'is_required' => 'boolean',
            'points'      => 'decimal:2',
        ];
    }

    public function requiresManualGrading(): bool
    {
        return $this->type === 'short_answer';
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }
}
