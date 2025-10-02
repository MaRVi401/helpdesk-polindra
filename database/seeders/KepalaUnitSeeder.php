<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Staff;

class KepalaUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Kepala Unit Layanan',
            'email' => 'kepala@gmail.com',
            'password' => bcrypt('123'),
            'role' => 'kepala_unit',
        ]);

        Staff::create([
            'user_id' => $user->id,
            'jabatan' => 'Kepala Unit Layanan'
        ]);
    }
}
