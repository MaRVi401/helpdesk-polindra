<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Staff;

class AdminUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user = User::create([
            'name' => 'Admin Layanan Unit',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123'),
            'role' => 'admin_unit',
        ]);

        Staff::create([
            'user_id' => $user->id,
            'jabatan' => 'Staff Layanan'
        ]);
    }
}
