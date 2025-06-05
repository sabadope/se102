<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $admin->role()->associate(Role::where('name', 'admin')->first());
        $admin->save();

        // Create Supervisor User
        $supervisor = User::create([
            'name' => 'Supervisor User',
            'email' => 'supervisor@example.com',
            'password' => Hash::make('password'),
        ]);
        $supervisor->role()->associate(Role::where('name', 'supervisor')->first());
        $supervisor->save();

        // Create Intern User
        $intern = User::create([
            'name' => 'Intern User',
            'email' => 'intern@example.com',
            'password' => Hash::make('password'),
        ]);
        $intern->role()->associate(Role::where('name', 'intern')->first());
        $intern->save();
    }
} 