<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'student_id_number', 'first_name', 'middle_name',
        'last_name', 'suffix', 'sex', 'date_of_birth', 'address',
        'contact_number', 'guardian_name', 'guardian_contact_number',
        'course', 'year_level', 'admission_status', 'photo_path',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix,
        ]);
        return implode(' ', $parts);
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bioNotes(): HasMany
    {
        return $this->hasMany(BioNote::class)->orderBy('created_at', 'desc');
    }
}
