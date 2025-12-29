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

    public function careerPaths(): BelongsToMany
    {
        return $this->belongsToMany(CareerPath::class, 'career_path_subject');
    }

    public function getDisplayName(): string
    {
        return $this->code . ' - ' . $this->name;
    }

    /**
     * Get semester type from subject code
     * Pattern: F23L1W001 where:
     *   F23 = program code
     *   L = level/location (constant)
     *   1 = year
     *   W/S = semester type (W=winter, S=summer/second)
     *   001 = subject number
     *
     * The letter after the year number determines semester type
     */
    public function getSemesterTypeFromCode(): string
    {
        // Match pattern like: F23L1W001 or F18L2S042
        // Extract the W or S after the year digit
        if (preg_match('/L\d([WS])/', $this->code, $matches)) {
            return $matches[1] === 'W' ? 'winter' : 'summer';
        }
        return $this->semester_type ?? 'winter'; // Fallback to stored value
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
