<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'team_id' => Team::factory(),
            'result' => $this->faker->randomElement([
                'success',
                'failure',
                'partial',
            ]),
            'final_note' => $this->faker->optional()->sentence(),
            'finished_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
