<?php

namespace App\Policies;

use App\Models\ProjectApplication;
use App\Models\User;

class ProjectApplicationPolicy
{
    public function view(User $user, ProjectApplication $application): bool
    {
        return $application->created_by_user_id === $user->id
            || $user->hasRole(['admin', 'mentor'])
            || $application->project->created_by_user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->student !== null;
    }

    public function update(User $user, ProjectApplication $application): bool
    {
        return $application->created_by_user_id === $user->id
            && $application->status === 'pending';
    }

    public function approve(User $user, ProjectApplication $application): bool
    {
        return $user->hasRole(['admin', 'mentor']);
    }

    public function reject(User $user, ProjectApplication $application): bool
    {
        return $user->hasRole(['admin', 'mentor']);
    }
}
