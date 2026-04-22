<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use App\Models\Student;
use App\Models\Mentor;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamMentor;
use App\Models\Project;
use App\Models\ProjectAssignment;

use Database\Seeders\UserSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\UserRolesSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --------------------
        // 1. FIX ADATOK (TE SYSTEMED)
        // --------------------
        $this->call([
            UserSeeder::class,
            RolesSeeder::class,
            UserRolesSeeder::class,
        ]);

        // --------------------
        // 2. COMPANIES
        // --------------------
        $companies = Company::factory(3)->create();

        // user-company kapcsolat (nálad marad)
        $users = User::all();

        foreach ($users as $index => $user) {
            $company = $companies[$index % $companies->count()];
            $user->companies()->attach($company->id);
        }

        // --------------------
        // 3. STUDENTS + MENTORS (USER-BŐL ÉPÍTVE)
        // --------------------

        $users = User::all();

        // fele student, fele mentor (egyszerű logika)
        $half = (int) floor($users->count() / 2);

        $studentUsers = $users->take($half);
        $mentorUsers = $users->skip($half);

        foreach ($studentUsers as $user) {
            Student::factory()->create([
                'user_id' => $user->id,
            ]);
        }

        foreach ($mentorUsers as $user) {
            Mentor::factory()->create([
                'user_id' => $user->id,
            ]);
        }

        $students = Student::all();
        $mentors = Mentor::all();

        // --------------------
        // 4. TEAMS
        // --------------------
        $teams = Team::factory(3)->create();

        foreach ($teams as $team) {
            $leader = $students->random();
            $team->leader_user_id = $leader->user_id;
            $team->save();
        }

        // --------------------
        // 5. TEAM MEMBERS
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

            // biztos leader bent van
            $leaderStudent = $students->firstWhere('user_id', $team->leader_user_id);

            if ($leaderStudent) {
                TeamMember::factory()->create([
                    'student_id' => $leaderStudent->id,
                    'team_id' => $team->id,
                    'member_role' => 'leader',
                ]);
            }
        }

        // --------------------
        // 6. TEAM MENTORS
        // --------------------
        foreach ($teams as $team) {
            $mentor = $mentors->random();

            TeamMentor::factory()->create([
                'mentor_id' => $mentor->id,
                'team_id' => $team->id,
            ]);
        }

        // --------------------
        // 7. PROJECTS + ASSIGNMENTS
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
