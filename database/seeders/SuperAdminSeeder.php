<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'sua@gmail.com',
            'password' => bcrypt('123'),
            'role' => 'super_admin',
        ]);

        \App\Models\Staff::create([
            'user_id' => $user->id,
            'jabatan' => 'Super Administrator'
        ]);
    }
}
