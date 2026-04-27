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

    public function removeMember(User $user, Team $team): bool
    {
        return $team->leader_user_id === $user->id;
    }

    public function activate(User $user, Team $team): bool
    {
        return $team->leader_user_id === $user->id;
    }
}
