<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->text(45),
            'description' => $this->faker->paragraphs(3, true),
            'type' => $this->faker->randomElement([
                'internal',
                'client',
                'research',
            ]),
            'created_by_user_id' => User::factory(),
            'company_id' => Company::factory(),
            'team_id' => Team::factory(),
            'budget' => $this->faker->randomFloat(2, 1000, 100000),
            'status' => $this->faker->randomElement([
                'pending',
                'active',
                'paused',
                'finished',
                'archived',
            ]),
            'deadline' => $this->faker->optional()->dateTimeBetween('+1 week', '+6 months'),
        ];
    }
}
