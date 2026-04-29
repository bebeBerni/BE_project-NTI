<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ProjectApplicationFactory extends Factory
{
public function definition(): array
{
    return [
        'project_id' => Project::query()->inRandomOrder()->first()?->id
            ?? Project::factory(),

        'team_id' => Team::query()->inRandomOrder()->first()?->id
            ?? Team::factory(),

        'category_id' => Category::query()->inRandomOrder()->first()?->id
            ?? Category::factory(),

        'submitted_by_user_id' => User::query()->inRandomOrder()->first()?->id
            ?? User::factory(),

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
