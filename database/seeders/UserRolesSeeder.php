<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
{
    $roles = Role::all()->keyBy('name');

    $admin = User::where('email', 'admin@test.com')->first();
    $student = User::where('email', 'student@test.com')->first();
    $teacher = User::where('email', 'teacher@test.com')->first();

    if ($admin && isset($roles['admin'])) {
        $admin->roles()->attach($roles['admin']->id);
    }

    if ($student && isset($roles['student'])) {
        $student->roles()->attach($roles['student']->id);
    }

    if ($teacher && isset($roles['mentor'])) {
        $teacher->roles()->attach($roles['mentor']->id);
    }
}
}
