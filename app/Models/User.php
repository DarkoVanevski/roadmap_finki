<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function progress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    public function getCompletedSubjectsForProgram($studyProgramId)
    {
        return $this->progress()
            ->where('study_program_id', $studyProgramId)
            ->where('status', 'completed')
            ->pluck('subject_id')
            ->toArray();
    }

    public function getInProgressSubjectsForProgram($studyProgramId)
    {
        return $this->progress()
            ->where('study_program_id', $studyProgramId)
            ->where('status', 'in_progress')
            ->pluck('subject_id')
            ->toArray();
    }
}
