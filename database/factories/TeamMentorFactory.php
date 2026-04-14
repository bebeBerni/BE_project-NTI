<?php

namespace Database\Factories;

use App\Models\Mentor;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamMentorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'mentor_id' => Mentor::factory(),
            'team_id' => Team::factory(),
            'assigned_at' => now(),
            'active' => true,
        ];
    }
}
