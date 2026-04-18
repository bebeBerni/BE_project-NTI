<?php

namespace Database\Factories;

use App\Models\Commission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommissionMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'users_id' => User::factory(),
            'commission_id' => Commission::factory(),
        ];
    }
}
