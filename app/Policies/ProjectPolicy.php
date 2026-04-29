<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        return $project->created_by_user_id === $user->id
            || $project->company_id === $user->companies()->first()?->id
            || $user->hasRole(['admin', 'mentor']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->companies()->exists();
    }

    public function update(User $user, Project $project): bool
    {
        return $project->created_by_user_id === $user->id || $user->hasRole('admin');
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->hasRole('admin');
    }
}
