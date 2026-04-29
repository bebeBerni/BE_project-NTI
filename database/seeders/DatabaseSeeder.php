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
use App\Models\Category;
use App\Models\Commission;
use App\Models\CommissionMember;
use App\Models\Decision;
use App\Models\ProjectApplication;
use App\Models\ProjectHistory;

use Database\Seeders\UserSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\UserRolesSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --------------------
        // 1. ZÁKLADNÉ SEEDRE
        // --------------------
        $this->call([
             RolesSeeder::class,
            UserSeeder::class,
            UserRolesSeeder::class,
            CategorySeeder::class,
        ]);

        // --------------------
        // 2. COMPANIES
        // --------------------
        $companies = Company::factory(5)->create();

        $users = User::all();

        foreach ($users as $index => $user) {
            $company = $companies[$index % $companies->count()];
            $user->companies()->attach($company->id);
        }

        // --------------------
        // 3. STUDENTS + MENTORS
        // --------------------
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
        $teams = Team::factory(5)->create();

        foreach ($teams as $team) {
            $leader = $students->random();
            $team->leader_user_id = $leader->user_id;
            $team->save();
        }

        // --------------------
        // 5. TEAM MEMBERS
        // --------------------
        foreach ($teams as $team) {
            $assignedStudents = $students->random(min(3, $students->count()));

            foreach ($assignedStudents as $index => $student) {
                TeamMember::factory()->create([
                    'student_id' => $student->id,
                    'team_id' => $team->id,
                    'member_role' => $index === 0 ? 'leader' : 'developer',
                ]);
            }

            $leaderStudent = $students->firstWhere('user_id', $team->leader_user_id);

            if ($leaderStudent && !TeamMember::where('team_id', $team->id)->where('student_id', $leaderStudent->id)->exists()) {
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
        // 7. CATEGORIES
        // --------------------
        $categories = Category::all();

        // --------------------
        // 8. PROJECTS
        // --------------------
        $projects = Project::factory(10)->create([
            'created_by_user_id' => fn() => $users->random()->id,
            'company_id' => fn() => $companies->random()->id,
            'team_id' => fn() => $teams->random()->id,
        ]);

        // --------------------
        // 9. PROJECT ASSIGNMENTS
        // --------------------
        foreach ($projects as $project) {
            $team = $teams->random();

            ProjectAssignment::factory()->create([
                'project_id' => $project->id,
                'team_id' => $team->id,
            ]);
        }

        // --------------------
        // 10. PROJECT APPLICATIONS
        // --------------------
        if ($categories->count() > 0) {
            ProjectApplication::factory(20)->create([
                'project_id' => fn() => $projects->random()->id,
                'team_id' => fn() => $teams->random()->id,
                'category_id' => fn() => $categories->random()->id,
            ]);
        }

        // --------------------
        // 11. PROJECT HISTORY
        // --------------------
        ProjectHistory::factory(20)->create([
            'project_id' => fn() => $projects->random()->id,
            'team_id' => fn() => $teams->random()->id,
        ]);

        // --------------------
        // 12. COMMISSIONS
        // --------------------
        $commissions = Commission::factory(5)->create();

        // --------------------
        // 13. DECISIONS
        // --------------------
        Decision::factory(10)->create([
            'project_id' => fn() => $projects->random()->id,
            'commission_id' => fn() => $commissions->random()->id,
        ]);

        // --------------------
        // 14. COMMISSION MEMBERS
        // --------------------
        CommissionMember::factory(15)->create([
            'user_id' => fn() => $users->random()->id,
            'commission_id' => fn() => $commissions->random()->id,
        ]);
    }
}
