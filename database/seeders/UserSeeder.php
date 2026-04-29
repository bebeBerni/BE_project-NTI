<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            'last_name'  => 'User',
            'email'      => 'admin@test.com',
            'password'   => Hash::make('123456'),
            'phone'      => '123456789'
        ]);

        User::create([
            'first_name' => 'John',
            'last_name'  => 'Student',
            'email'      => 'student@test.com',
            'password'   => Hash::make('123456'),
            'phone'      => '987654321'
        ]);

        User::create([
            'first_name' => 'Jane',
            'last_name'  => 'Mentor',
            'email'      => 'mentor@test.com',
            'password'   => Hash::make('123456'),
            'phone'      => '555555555'
        ]);
    }
}
