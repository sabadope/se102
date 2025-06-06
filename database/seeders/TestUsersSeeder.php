<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Create supervisor user
        User::create([
            'name' => 'Supervisor User',
            'email' => 'supervisor@gmail.com',
            'password' => Hash::make('Supervisor123!'),
            'role' => 'supervisor',
        ]);

        // Create intern user
        User::create([
            'name' => 'Intern User',
            'email' => 'intern@gmail.com',
            'password' => Hash::make('Intern123!'),
            'role' => 'intern',
        ]);
    }
} 