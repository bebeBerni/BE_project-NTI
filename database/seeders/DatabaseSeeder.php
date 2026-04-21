<?php

namespace Database\Seeders;

use App\Models\Mentor;
use App\Models\Project;
use App\Models\ProjectAssignment;
use App\Models\Student;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamMentor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Role;
use App\Models\Company;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $role = Role::factory()->create();
        $company = Company::factory()->create();

        // --------------------
        // STUDENTS
        // --------------------
        User::factory(5)->create()->each(function ($user) use ($role, $company) {
            $user->roles()->attach($role->id);
            $user->companies()->attach($company->id);

            Student::factory()->create([
                'user_id' => $user->id,
            ]);
        });

        // --------------------
        // MENTORS
        // --------------------
        User::factory(5)->create()->each(function ($user) use ($role, $company) {
            $user->roles()->attach($role->id);
            $user->companies()->attach($company->id);

            Mentor::factory()->create([
                'user_id' => $user->id,
            ]);
        });

        $students = Student::all();
        $mentors = Mentor::all();

        // --------------------
        // TEAMS (FIXED LEADERS)
        // --------------------
        $teams = Team::factory(3)->make()->each(function ($team) use ($students) {
            $leader = $students->random();

            $team->leader_user_id = $leader->user_id;
            $team->save();
        });

        // --------------------
        // TEAM MEMBERS
        // --------------------
        foreach ($teams as $team) {
            $assignedStudents = $students->random(3);

            foreach ($assignedStudents as $index => $student) {
                TeamMember::factory()->create([
                    'student_id' => $student->id,
                    'team_id' => $team->id,
                    'member_role' => $index === 0 ? 'leader' : 'developer',
                ]);
            }

            // Ensure leader is in the team
            $leaderStudent = $students->firstWhere('user_id', $team->leader_user_id);

            TeamMember::factory()->create([
                'student_id' => $leaderStudent->id,
                'team_id' => $team->id,
                'member_role' => 'leader',
            ]);
        }

        // --------------------
        // TEAM MENTORS
        // --------------------
        foreach ($teams as $team) {
            $mentor = $mentors->random();

            TeamMentor::factory()->create([
                'mentor_id' => $mentor->id,
                'team_id' => $team->id,
            ]);
        }

        // --------------------
        // PROJECTS + ASSIGNMENTS
        // --------------------
        $projects = Project::factory(5)->create();

        foreach ($projects as $project) {
            $team = $teams->random();

            ProjectAssignment::factory()->create([
                'project_id' => $project->id,
                'team_id' => $team->id,
            ]);
        }
    }
}
