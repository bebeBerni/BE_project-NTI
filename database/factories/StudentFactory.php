<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'faculty' => fake()->randomElement(['FPVaI', 'FF', 'PF','FSS', 'FSVaZ']),
            'department' => fake()->word(),
            'study_program' => fake()->randomElement(['Umelá inteligenica', 'Aplikovaná informatika', 'Učitelstvo informatiky v kombinácií']),
            'year_of_study' => fake()->numberBetween(1, 5),
            'is_ukf_verified' => fake()->boolean(),
        ];
    }
}
