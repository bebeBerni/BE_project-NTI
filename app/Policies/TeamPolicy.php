<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function create(User $user): bool
    {
        return $user->student !== null;
    }

    public function update(User $user, Team $team): bool
    {
        return $team->leader_user_id === $user->id;
    }

    public function addMember(User $user, Team $team): bool
    {
        return $team->leader_user_id === $user->id;
    }

    public function removeMember(User $user, Team $team, $studentId): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if (!$user->mentor) {
            return false;
        }

        return $team->mentors()
            ->where('mentor_id', $user->mentor->id)
            ->exists();
    }

    public function activate(User $user, Team $team): bool
    {
        return $team->leader_user_id === $user->id;
    }
    public function view(User $user, Team $team): bool
    {
        return $team->students()->where('student_id', $user->student->id)->exists()
            || $team->leader_user_id === $user->id
            || $user->hasRole('admin');
    }

    public function delete(User $user, Team $team): bool
    {
        return $team->leader_user_id === $user->id || $user->hasRole('admin');
    }
}
