<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Role;
use App\Models\Company;
use App\Models\Document;
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

        $user = User::factory()->create();
        $role = Role::factory()->create();
        $company = Company::factory()->create();

        $user->roles()->attach($role->id);
        $user->companies()->attach($company->id);
    }
}

