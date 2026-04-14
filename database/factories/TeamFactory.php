<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'leader_user_id' => User::factory(),
            'status' => fake()->randomElement(['active', 'inactive', 'archived']),
        ];
    }
}
