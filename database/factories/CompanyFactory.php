<?php

namespace Database\Factories;
use App\Models\User;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'company_name' => fake()->company(),
        'ico' => fake()->unique()->numerify('#########'),
        'description' => fake()->sentence(),
        'website' => fake()->url(),
        'address' => fake()->address(),
        'users_id' => User::factory(), // owner
        ];
    }
}
