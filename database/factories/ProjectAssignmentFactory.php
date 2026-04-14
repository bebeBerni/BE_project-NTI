<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectAssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'team_id' => Team::factory(),
            'assigned_at' => now(),
            'status' => fake()->randomElement(['assigned', 'in_progress', 'completed']),
        ];
    }
}
