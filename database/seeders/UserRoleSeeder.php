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
            User::create(['name' => 'Ahmad Yassin', 'email' => 'sua@email.com', 'password' => bcrypt('12345678'), 'role' => 'super_admin'])
                ->staff()->create(['unit_id' => $unitTikId, 'jabatan_id' => $jabatanSuperAdminId, 'nik' => '3201010101000001']);

            // 2. Buat Kepala Unit
            $kepalaUnitUser = User::create(['name' => 'Kepala Unit TIK', 'email' => 'kepala@email.com', 'password' => bcrypt('12345678'), 'role' => 'kepala_unit']);
            $kepalaUnitStaff = Staff::create(['user_id' => $kepalaUnitUser->id, 'unit_id' => $unitTikId, 'jabatan_id' => $jabatanKepalaId, 'nik' => '3201010202000002']);
            Unit::find($unitTikId)->update(['kepala_id' => $kepalaUnitStaff->id]);

            // 3. Buat Admin Unit
            User::create(['name' => 'Admin Unit TIK', 'email' => 'admin@email.com', 'password' => bcrypt('12345678'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitTikId, 'jabatan_id' => $jabatanStaffId, 'nik' => '3201010303000003']);

            // 4. Staff UPA TIK
            User::create(['name' => 'Staff UPA TIK', 'email' => 'staff.tik@email.com', 'password' => bcrypt('12345678'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitTikId, 'jabatan_id' => $jabatanStaffId, 'nik' => '3201010404000004']);

            // 5. Staff Akademik
            User::create(['name' => 'Staff Akademik', 'email' => 'staff.akademik@email.com', 'password' => bcrypt('12345678'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitAkademikId, 'jabatan_id' => $jabatanStaffId, 'nik' => '3201010505000005']);

            // 6. Staff Kemahasiswaan
            User::create(['name' => 'Staff Kemahasiswaan', 'email' => 'staff.kemahasiswaan@email.com', 'password' => bcrypt('12345678'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitKemahasiswaanId, 'jabatan_id' => $jabatanStaffId, 'nik' => '3201010606000006']);

            // 7. Tugimin Atmajo (Akademik)
            User::create(['name' => 'Tugimin Atmajo', 'email' => 'tugimin@email.com', 'password' => bcrypt('12345678'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitAkademikId, 'jabatan_id' => $jabatanStaffId, 'nik' => '3201010707000007']);

            // 8. Bunga Citra Lestari (Kemahasiswaan)
            User::create(['name' => 'Bunga Citra Lestari', 'email' => 'citra@email.com', 'password' => bcrypt('12345678'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitKemahasiswaanId, 'jabatan_id' => $jabatanStaffId, 'nik' => '3201010808000008']);

            // 9. Febri Mulyadi (UPA TIK)
            User::create(['name' => 'Febri Mulyadi', 'email' => 'febri@example.com', 'password' => bcrypt('12345678'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitTikId, 'jabatan_id' => $jabatanStaffId, 'nik' => '3201010909000009']);

        } catch (\Exception $e) {
            // Memberikan pesan error yang lebih jelas di konsol jika seeder gagal
            $this->command->error('Gagal menjalankan UserRoleSeeder: ' . $e->getMessage());
        }
    }
}