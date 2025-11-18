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
            // Ambil data master
            $jabatanSuperAdminId = Jabatan::where('nama_jabatan', 'Super Administrator')->firstOrFail()->id;
            $jabatanKepalaId = Jabatan::where('nama_jabatan', 'Kepala Unit')->firstOrFail()->id;
            $jabatanStaffId = Jabatan::where('nama_jabatan', 'Staff Layanan')->firstOrFail()->id;

            $unitTikId = Unit::where('nama_unit', 'UPA TIK')->firstOrFail()->id;
            $unitAkademikId = Unit::where('nama_unit', 'Akademik')->firstOrFail()->id;
            $unitKemahasiswaanId = Unit::where('nama_unit', 'Kemahasiswaan')->firstOrFail()->id;


            // /** ======================
            //  * 1. SUPER ADMIN → UPA TIK
            //  ========================*/
            $superAdmin = User::firstOrCreate(
                ['email' => 'ahmadyassin@email.com'],
                ['name' => 'Ahmad Yassin', 'password' => bcrypt('12345678'), 'role' => 'super_admin']
            );

            $superAdmin->staff()->updateOrCreate(
                ['user_id' => $superAdmin->id],
                ['unit_id' => $unitTikId, 'jabatan_id' => $jabatanSuperAdminId, 'nik' => '3201010101000001']
            );


            // /** =========================
            //  * 2. KEPALA UNIT → UPA TIK
            //  ===========================*/
            $kepalaUnitUser = User::firstOrCreate(
                ['email' => 'adisuheryadi@email.com'],
                ['name' => 'Adi Suheryadi', 'password' => bcrypt('12345678'), 'role' => 'kepala_unit']
            );

            $kepalaUnitStaff = Staff::updateOrCreate(
                ['user_id' => $kepalaUnitUser->id],
                ['unit_id' => $unitTikId, 'jabatan_id' => $jabatanKepalaId, 'nik' => '3201010202000002']
            );

            Unit::find($unitTikId)->update(['kepala_id' => $kepalaUnitStaff->id]);


            // /** ============================
            // * 3. ADMIN UNIT → AKADEMIK
            //  ===============================*/
            $adminAkademik = User::firstOrCreate(
                ['email' => 'fachrizal@email.com'],
                ['name' => 'Fachrizal Bachar', 'password' => bcrypt('12345678'), 'role' => 'admin_unit']
            );

            $adminAkademik->staff()->updateOrCreate(
                ['user_id' => $adminAkademik->id],
                ['unit_id' => $unitAkademikId, 'jabatan_id' => $jabatanStaffId, 'nik' => '3201010303000003']
            );


            // /** ============================
            //  * 4. ADMIN KEMAHASISWAAN
            //  ===============================*/
            $kemahasiswaanUser = User::firstOrCreate(
                ['email' => 'citra@email.com'],
                ['name' => 'Bunga Citra Lestari', 'password' => bcrypt('12345678'), 'role' => 'admin_unit']
            );

            $kemahasiswaanUser->staff()->updateOrCreate(
                ['user_id' => $kemahasiswaanUser->id],
                ['unit_id' => $unitKemahasiswaanId, 'jabatan_id' => $jabatanStaffId, 'nik' => '3201010808000008']
            );


            // /** ============================
            //  * 5. ADMIN UPA TIK
            //  ===============================*/
            $febri = User::firstOrCreate(
                ['email' => 'febri@example.com'],
                ['name' => 'Febri Mulyadi', 'password' => bcrypt('12345678'), 'role' => 'admin_unit']
            );

            $febri->staff()->updateOrCreate(
                ['user_id' => $febri->id],
                ['unit_id' => $unitTikId, 'jabatan_id' => $jabatanStaffId, 'nik' => '3201010909000009']
            );

            $this->command->info("UserRoleSeeder berhasil dijalankan!");

        } catch (\Exception $e) {

            $this->command->error('Gagal menjalankan UserRoleSeeder: ' . $e->getMessage());
        }
    }

}