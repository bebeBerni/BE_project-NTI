<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'team_id' => Team::factory(),
            'category_id' => Category::factory(),
            'status' => $this->faker->randomElement([
                'pending',
                'approved',
                'rejected',
            ]),
            'motivation' => $this->faker->paragraphs(2, true),
            'note' => $this->faker->optional()->sentence(),
            'applied_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
