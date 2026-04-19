<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CommissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Hodnotiaca komisia',
                'Výberová komisia',
                'Odborná komisia',
                'Projektová komisia',
                'Schvaľovacia komisia',
            ]),
            'description' => $this->faker->optional()->sentence(),
            'status' => $this->faker->randomElement([
                'active',
                'inactive',
                'closed',
            ]),
        ];
    }
}
