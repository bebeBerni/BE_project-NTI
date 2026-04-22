<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Category;
use App\Models\Commission;
use App\Models\CommissionMember;
use App\Models\Company;
use App\Models\Decision;
use App\Models\Project;
use App\Models\ProjectApplication;
use App\Models\ProjectHistory;
use App\Models\Role;
use App\Models\Team;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
        ]);

        // --- Shared base records ---
        $users     = User::factory(10)->create();
        $roles     = Role::factory(3)->create();
        $companies = Company::factory(5)->create();
        $teams     = Team::factory(5)->create();
        $categories = Category::all();

        // Attach roles and companies to users
        $users->each(function (User $user) use ($roles, $companies) {
            $user->roles()->attach($roles->random()->id);
            $user->companies()->attach($companies->random()->id);
        });

        // --- Projects (reuse existing companies, teams & users) ---
        $projects = Project::factory(10)->create([
            'created_by_user_id' => fn() => $users->random()->id,
            'company_id'         => fn() => $companies->random()->id,
            'team_id'            => fn() => $teams->random()->id,
        ]);

        // --- ProjectApplications (reuse existing projects, teams & categories) ---
        ProjectApplication::factory(20)->create([
            'project_id'  => fn() => $projects->random()->id,
            'team_id'     => fn() => $teams->random()->id,
            'category_id' => fn() => $categories->random()->id,
        ]);

        // --- ProjectHistory (reuse existing projects & teams) ---
        ProjectHistory::factory(20)->create([
            'project_id' => fn() => $projects->random()->id,
            'team_id'    => fn() => $teams->random()->id,
        ]);

        // --- Commissions ---
        $commissions = Commission::factory(5)->create();

        // --- Decisions (reuse existing projects & commissions) ---
        Decision::factory(10)->create([
            'project_id'    => fn() => $projects->random()->id,
            'commission_id' => fn() => $commissions->random()->id,
        ]);

        // --- CommissionMembers (reuse existing users & commissions) ---
        CommissionMember::factory(15)->create([
            'user_id'       => fn() => $users->random()->id,
            'commission_id' => fn() => $commissions->random()->id,
        ]);
    }
}

