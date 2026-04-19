<?php

namespace Database\Factories;
use App\Models\User;
use App\Models\Document;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'users_id' => User::factory(),
       'teams_id' => Team::factory(),
        'type' => 'pdf',
        'file_name' => fake()->word() . '.pdf',
        'file_path' => '/files/' . fake()->word() . '.pdf',
        'uploaded_at' => now(),
        ];
    }
}
