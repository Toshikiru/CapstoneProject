<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by', 'title', 'description', 'instructions',
        'time_limit', 'passing_score', 'access_code', 'is_active',
        'max_attempts', 'available_from', 'available_until',
    ];

    protected function casts(): array
    {
        return [
            'is_active'      => 'boolean',
            'available_from' => 'datetime',
            'available_until'=> 'datetime',
            'passing_score'  => 'decimal:2',
        ];
    }

    public static function generateAccessCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (static::withTrashed()->where('access_code', $code)->exists());

        return $code;
    }

    public function isAvailable(): bool
    {
        if (! $this->is_active) {
            return false;
        }
        $now = now();
        if ($this->available_from && $now->lt($this->available_from)) {
            return false;
        }
        if ($this->available_until && $now->gt($this->available_until)) {
            return false;
        }
        return true;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function examSessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }

    public function scoreInterpretations(): HasMany
    {
        return $this->hasMany(ScoreInterpretation::class);
    }

    public function getTotalPointsAttribute(): float
    {
        return $this->questions()->sum('points');
    }

    public function getTotalQuestionsAttribute(): int
    {
        return $this->questions()->count();
    }
}
