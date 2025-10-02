<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Staff;
use App\Models\Jabatan;
use App\Models\Unit;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID dari data master yang sudah dibuat
        $jabatanSuperAdminId = Jabatan::where('nama_jabatan', 'Super Administrator')->first()->id;
        $jabatanKepalaId = Jabatan::where('nama_jabatan', 'Kepala Unit')->first()->id;
        $jabatanStaffId = Jabatan::where('nama_jabatan', 'Staff Layanan')->first()->id;
        $unitTikId = Unit::where('nama_unit', 'UPT TIK')->first()->id;

        // 1. Buat Super Admin
        $superAdminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'sua@gmail.com',
            'password' => bcrypt('123'),
            'role' => 'super_admin',
        ]);
        Staff::create([
            'user_id' => $superAdminUser->id,
            'unit_id' => $unitTikId,
            'jabatan_id' => $jabatanSuperAdminId,
            'nik' => '1111111111111111'
        ]);

        // 2. Buat Kepala Unit
        $kepalaUnitUser = User::create([
            'name' => 'Kepala Unit TIK',
            'email' => 'kepala@gmail.com',
            'password' => bcrypt('123'),
            'role' => 'kepala_unit',
        ]);
        $kepalaUnitStaff = Staff::create([
            'user_id' => $kepalaUnitUser->id,
            'unit_id' => $unitTikId,
            'jabatan_id' => $jabatanKepalaId,
            'nik' => '2222222222222222'
        ]);

        // Update unit untuk menunjuk kepala unit yang baru dibuat
        $unitTik = Unit::find($unitTikId);
        $unitTik->kepala_id = $kepalaUnitStaff->id;
        $unitTik->save();

        // 3. Buat Admin Unit
        $adminUnitUser = User::create([
            'name' => 'Admin Unit TIK',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123'),
            'role' => 'admin_unit',
        ]);
        Staff::create([
            'user_id' => $adminUnitUser->id,
            'unit_id' => $unitTikId,
            'jabatan_id' => $jabatanStaffId,
            'nik' => '3333333333333333'
        ]);
    }
}