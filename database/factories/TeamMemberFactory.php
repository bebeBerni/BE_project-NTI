<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'team_id' => Team::factory(),
            'member_role' => fake()->randomElement(['leader', 'developer', 'designer']),
            'joined_at' => now(),
        ];
    }
}
