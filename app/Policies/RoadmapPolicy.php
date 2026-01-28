<?php

namespace App\Policies;

use App\Models\Roadmap;
use App\Models\User;

class RoadmapPolicy
{
    /**
     * Determine whether the user can view the roadmap
     */
    public function view(User $user, Roadmap $roadmap): bool
    {
        return $user->id === $roadmap->user_id;
    }

    /**
     * Determine whether the user can delete the roadmap
     */
    public function delete(User $user, Roadmap $roadmap): bool
    {
        return $user->id === $roadmap->user_id;
    }
}
