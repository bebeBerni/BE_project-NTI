<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MentorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'specialization' => fake()->randomElement(['Vývoj softvéru', 'AI a dátové technológie', 'Webové aplikácie','Herný vývoj','IoT a embedded systémy']),
            'bio' => fake()->sentence(),
        ];
    }
}
