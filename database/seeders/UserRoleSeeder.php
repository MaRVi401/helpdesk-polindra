<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Staff;
use App\Models\Jabatan;
use App\Models\Unit;
use Illuminate\Support\Facades\Log;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Ambil ID dari data master yang sudah dibuat
            $jabatanSuperAdminId = Jabatan::where('nama_jabatan', 'Super Administrator')->firstOrFail()->id;
            $jabatanKepalaId = Jabatan::where('nama_jabatan', 'Kepala Unit')->firstOrFail()->id;
            $jabatanStaffId = Jabatan::where('nama_jabatan', 'Staff Layanan')->firstOrFail()->id;

            // Ambil ID untuk setiap unit
            $unitTikId = Unit::where('nama_unit', 'UPA TIK')->firstOrFail()->id;
            $unitAkademikId = Unit::where('nama_unit', 'Akademik')->firstOrFail()->id;
            $unitKemahasiswaanId = Unit::where('nama_unit', 'Kemahasiswaan')->firstOrFail()->id;

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
                'nik' => '11111'
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
                'nik' => '22222'
            ]);

            $unitTik = Unit::find($unitTikId);
            if ($unitTik) {
                $unitTik->kepala_id = $kepalaUnitStaff->id;
                $unitTik->save();
            }

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
                'nik' => '33333'
            ]);


            // 4. Buat Staff Layanan untuk Unit UPA TIK
            $staffTikUser = User::create([
                'name' => 'Staff UPA TIK',
                'email' => 'staff.tik@gmail.com',
                'password' => bcrypt('123'),
                'role' => 'admin_unit', // Role bisa disesuaikan
            ]);
            Staff::create([
                'user_id' => $staffTikUser->id,
                'unit_id' => $unitTikId,
                'jabatan_id' => $jabatanStaffId,
                'nik' => '44444'
            ]);

            // 5. Buat Staff Layanan untuk Unit Akademik
            $staffAkademikUser = User::create([
                'name' => 'Staff Akademik',
                'email' => 'staff.akademik@gmail.com',
                'password' => bcrypt('123'),
                'role' => 'admin_unit',
            ]);
            Staff::create([
                'user_id' => $staffAkademikUser->id,
                'unit_id' => $unitAkademikId,
                'jabatan_id' => $jabatanStaffId,
                'nik' => '55555'
            ]);

            // 6. Buat Staff Layanan untuk Unit Kemahasiswaan
            $staffKemahasiswaanUser = User::create([
                'name' => 'Staff Kemahasiswaan',
                'email' => 'staff.kemahasiswaan@gmail.com',
                'password' => bcrypt('123'),
                'role' => 'admin_unit',
            ]);
            Staff::create([
                'user_id' => $staffKemahasiswaanUser->id,
                'unit_id' => $unitKemahasiswaanId,
                'jabatan_id' => $jabatanStaffId,
                'nik' => '66666'
            ]);


        } catch (\Exception $e) {
            // Memberikan pesan error yang lebih jelas di konsol jika seeder gagal
            $this->command->error('Gagal menjalankan UserRoleSeeder: ' . $e->getMessage());
        }
    }
}