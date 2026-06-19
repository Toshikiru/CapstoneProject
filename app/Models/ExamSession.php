<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ExamSession extends Model
{
    protected $fillable = [
        'user_id', 'exam_id', 'session_token', 'status',
        'started_at', 'submitted_at', 'expires_at', 'time_remaining',
        'raw_score', 'percentage', 'interpretation', 'result_status',
        'is_graded', 'ip_address', 'browser_info', 'focus_loss_count',
    ];

    protected function casts(): array
    {
        return [
            'started_at'   => 'datetime',
            'submitted_at' => 'datetime',
            'expires_at'   => 'datetime',
            'is_graded'    => 'boolean',
            'raw_score'    => 'decimal:2',
            'percentage'   => 'decimal:2',
        ];
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public function isExpired(): bool
    {
        return $this->expires_at === null || $this->expires_at->isPast();
    }

    public function isActive(): bool
    {
        return $this->status === 'in_progress' && ! $this->isExpired();
    }

    public function getTimeRemainingInSecondsAttribute(): int
    {
        if ($this->status !== 'in_progress' || $this->expires_at === null) {
            return 0;
        }
        // Explicit Unix timestamp subtraction avoids any ambiguity around
        // Carbon's diffInSeconds() argument-order/sign conventions.
        return max(0, $this->expires_at->getTimestamp() - now()->getTimestamp());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
