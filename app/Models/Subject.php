<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_mk',
        'description',
        'description_mk',
        'semester_type',
        'year',
        'subject_type',
        'instructors',
        'credits',
        'total_hours',
        'lecture_hours',
        'practice_hours',
    ];

    public function studyPrograms(): BelongsToMany
    {
        return $this->belongsToMany(StudyProgram::class, 'study_program_subject')
            ->withPivot('order', 'type')
            ->orderBy('study_program_subject.order');
    }

    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            Subject::class,
            'subject_prerequisites',
            'subject_id',
            'prerequisite_id'
        );
    }

    public function requiredBy(): BelongsToMany
    {
        return $this->belongsToMany(
            Subject::class,
            'subject_prerequisites',
            'prerequisite_id',
            'subject_id'
        );
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    public function getDisplayName(): string
    {
        return $this->code . ' - ' . $this->name;
    }

    public function hasPrerequisitesMet($userId, $studyProgramId): bool
    {
        $prerequisites = $this->prerequisites()->get();

        if ($prerequisites->isEmpty()) {
            return true;
        }

        foreach ($prerequisites as $prerequisite) {
            $completed = UserProgress::where('user_id', $userId)
                ->where('subject_id', $prerequisite->id)
                ->where('study_program_id', $studyProgramId)
                ->where('status', 'completed')
                ->exists();

            if (!$completed) {
                return false;
            }
        }

        return true;
    }
}
