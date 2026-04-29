<?php

namespace App\Policies;

use App\Models\Commission;
use App\Models\User;

class CommissionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Commission $commission): bool
    {
        return $commission->members()->where('user_id', $user->id)->exists()
            || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function addMember(User $user, Commission $commission): bool
    {
        return $user->hasRole('admin');
    }

    public function removeMember(User $user, Commission $commission): bool
    {
        return $user->hasRole('admin');
    }
}
