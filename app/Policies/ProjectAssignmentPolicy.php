<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ProjectAssignment;

class ProjectAssignmentPolicy
{
    /**
     * Vytvorenie assignmentu (assign team → project)
     */
    public function create(User $user): bool
    {
        return $user->company_id !== null
            || in_array($user->role, ['admin', 'mentor']);
    }

    /**
     * Zobrazenie assignmentu
     */
    public function view(User $user, ProjectAssignment $assignment): bool
    {
        if ($assignment->team->leader_user_id === $user->id) {
            return true;
        }

        if ($assignment->project->company_id === $user->company_id) {
            return true;
        }

        return in_array($user->role, ['admin', 'mentor']);
    }

    /**
     * Zrušenie assignmentu (unassign)
     */
    public function delete(User $user, ProjectAssignment $assignment): bool
    {
        if ($assignment->project->company_id === $user->company_id) {
            return true;
        }

        return $user->role === 'admin';
    }

    /**
     * Update status (napr. active, finished)
     */
    public function update(User $user, ProjectAssignment $assignment): bool
    {
        if ($assignment->project->company_id === $user->company_id) {
            return true;
        }

        return in_array($user->role, ['admin', 'mentor']);
    }
}
