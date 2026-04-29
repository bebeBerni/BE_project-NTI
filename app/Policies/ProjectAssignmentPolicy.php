<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ProjectAssignment;

class ProjectAssignmentPolicy
{
    /**
     * Admin/Mentor can see all assignments
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'mentor']);
    }

    /**
     * View a specific assignment
     */
    public function view(User $user, ProjectAssignment $assignment): bool
    {
        if ($assignment->team->leader_user_id === $user->id) {
            return true;
        }

        if ($assignment->project->company_id &&
            $user->companies()->where('company_id', $assignment->project->company_id)->exists()) {
            return true;
        }

        if ($assignment->project->created_by_user_id === $user->id) {
            return true;
        }

        return $user->hasRole(['admin', 'mentor']);
    }

    /**
     * Create assignment (assign team to project)
     */
    public function create(User $user): bool
    {
        if ($user->companies()->exists()) {
            return true;
        }

        return $user->hasRole(['admin', 'mentor']);
    }

    /**
     * Update assignment status
     */
    public function update(User $user, ProjectAssignment $assignment): bool
    {
        if ($assignment->project->created_by_user_id === $user->id) {
            return true;
        }

        if ($assignment->project->company_id &&
            $user->companies()->where('company_id', $assignment->project->company_id)->exists()) {
            return true;
        }

        return $user->hasRole(['admin', 'mentor']);
    }

    /**
     * Delete/unassign from project
     */
    public function delete(User $user, ProjectAssignment $assignment): bool
    {
        if ($assignment->project->created_by_user_id === $user->id) {
            return true;
        }

        if ($assignment->project->company_id &&
            $user->companies()->where('company_id', $assignment->project->company_id)->exists()) {
            return true;
        }

        return $user->hasRole('admin');
    }
}
