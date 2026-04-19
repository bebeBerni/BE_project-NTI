<?php

namespace Database\Factories;

use App\Models\Commission;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class DecisionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'commission_id' => Commission::factory(),
            'status' => $this->faker->randomElement([
                'pending',
                'approved',
                'rejected',
                'revision',
            ]),
            'comment' => $this->faker->optional()->sentence(),
            'decided_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
