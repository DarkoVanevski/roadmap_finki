<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Roadmap extends Model
{
    protected $table = 'roadmaps';

    protected $fillable = [
        'user_id',
        'study_program_id',
        'career_path_id',
        'roadmap_data',
        'semester_roadmap_data',
    ];

    protected $casts = [
        'roadmap_data' => 'array',
        'semester_roadmap_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function careerPath(): BelongsTo
    {
        return $this->belongsTo(CareerPath::class);
    }
}
