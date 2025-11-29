<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyProgram extends Model
{
    protected $fillable = [
        'name_mk',
        'name_en',
        'description_mk',
        'description_en',
        'duration_years',
        'cycle',
        'code',
    ];

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'study_program_subject')
            ->withPivot('order', 'type')
            ->orderBy('study_program_subject.order');
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    public function getMandatorySubjects()
    {
        return $this->subjects()
            ->wherePivot('type', 'mandatory')
            ->get();
    }

    public function getElectiveSubjects()
    {
        return $this->subjects()
            ->wherePivot('type', 'elective')
            ->get();
    }

    public function getDisplayName(): string
    {
        return $this->name_mk . ' (' . $this->duration_years . ' години)';
    }
}
