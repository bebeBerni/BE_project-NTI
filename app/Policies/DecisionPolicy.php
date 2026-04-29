<?php

namespace App\Policies;

use App\Models\Decision;
use App\Models\User;

class DecisionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->commissionMembers()->exists() || $user->hasRole('admin');
    }

    public function view(User $user, Decision $decision): bool
    {
        return $decision->commission->members()
                ->where('user_id', $user->id)->exists()
            || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->commissionMembers()->exists() || $user->hasRole('admin');
    }

    public function update(User $user, Decision $decision): bool
    {
        return $decision->commission->members()
                ->where('user_id', $user->id)->exists()
            || $user->hasRole('admin');
    }
}
