<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserRolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::all()->keyBy('name');

        $users = [
            'admin@test.com'   => 'admin',
            'student@test.com' => 'student',
            'mentor@test.com'  => 'mentor',
        ];

        foreach ($users as $email => $roleName) {

            $user = User::where('email', $email)->first();

            if ($user && isset($roles[$roleName])) {
                $user->roles()->syncWithoutDetaching($roles[$roleName]->id);
            }
        }
    }
}
