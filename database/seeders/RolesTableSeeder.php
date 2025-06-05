<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Role::insert([
            [
                'name' => 'admin',
                'description' => 'System Administrator',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'supervisor',
                'description' => 'Intern Supervisor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'intern',
                'description' => 'Student Intern',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 