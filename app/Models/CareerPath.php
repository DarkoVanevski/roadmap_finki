<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CareerPath extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'career_path_subject')
            ->withPivot('order', 'is_required')
            ->orderBy('career_path_subject.order');
    }

    public function userProgress(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }
}
