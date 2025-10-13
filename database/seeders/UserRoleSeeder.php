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
            User::create(['name' => 'Super Admin', 'email' => 'sua@gmail.com', 'password' => bcrypt('12345678'), 'role' => 'super_admin'])
                ->staff()->create(['unit_id' => $unitTikId, 'jabatan_id' => $jabatanSuperAdminId, 'nik' => '11111']);

            // 2. Buat Kepala Unit
            $kepalaUnitUser = User::create(['name' => 'Kepala Unit TIK', 'email' => 'kepala@gmail.com', 'password' => bcrypt('12345678'), 'role' => 'kepala_unit']);
            $kepalaUnitStaff = Staff::create(['user_id' => $kepalaUnitUser->id, 'unit_id' => $unitTikId, 'jabatan_id' => $jabatanKepalaId, 'nik' => '22222']);
            Unit::find($unitTikId)->update(['kepala_id' => $kepalaUnitStaff->id]);

            // 3. Buat Admin Unit
            User::create(['name' => 'Admin Unit TIK', 'email' => 'admin@gmail.com', 'password' => bcrypt('12345678'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitTikId, 'jabatan_id' => $jabatanStaffId, 'nik' => '33333']);

            // 4. Buat Staff Layanan untuk Unit UPA TIK
            User::create(['name' => 'Staff UPA TIK', 'email' => 'staff.tik@gmail.com', 'password' => bcrypt('12345678'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitTikId, 'jabatan_id' => $jabatanStaffId, 'nik' => '44444']);

            // 5. Buat Staff Layanan untuk Unit Akademik
            User::create(['name' => 'Staff Akademik', 'email' => 'staff.akademik@gmail.com', 'password' => bcrypt('12345678'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitAkademikId, 'jabatan_id' => $jabatanStaffId, 'nik' => '55555']);

            // 6. Buat Staff Layanan untuk Unit Kemahasiswaan
            User::create(['name' => 'Staff Kemahasiswaan', 'email' => 'staff.kemahasiswaan@gmail.com', 'password' => bcrypt('12345678'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitKemahasiswaanId, 'jabatan_id' => $jabatanStaffId, 'nik' => '66666']);

            // --- TAMBAHAN 9 USER STAFF MANUAL ---

            // 7. Staff Layanan 1 (Akademik)
            User::create(['name' => 'Budi Santoso', 'email' => 'budi.s@example.com', 'password' => bcrypt('12345678'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitAkademikId, 'jabatan_id' => $jabatanStaffId, 'nik' => '77701']);

            // 8. Staff Layanan 2 (Kemahasiswaan)
            User::create(['name' => 'Citra Lestari', 'email' => 'citra.l@example.com', 'password' => bcrypt('123'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitKemahasiswaanId, 'jabatan_id' => $jabatanStaffId, 'nik' => '77702']);

            // 9. Staff Layanan 3 (UPA TIK)
            User::create(['name' => 'Dedi Wijaya', 'email' => 'dedi.w@example.com', 'password' => bcrypt('123'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitTikId, 'jabatan_id' => $jabatanStaffId, 'nik' => '77703']);

            // 10. Staff Layanan 4 (Akademik)
            User::create(['name' => 'Eka Putri', 'email' => 'eka.p@example.com', 'password' => bcrypt('123'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitAkademikId, 'jabatan_id' => $jabatanStaffId, 'nik' => '77704']);

            // 11. Staff Layanan 5 (Kemahasiswaan)
            User::create(['name' => 'Fitri Handayani', 'email' => 'fitri.h@example.com', 'password' => bcrypt('123'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitKemahasiswaanId, 'jabatan_id' => $jabatanStaffId, 'nik' => '77705']);

            // 12. Staff Layanan 6 (UPA TIK)
            User::create(['name' => 'Gilang Ramadhan', 'email' => 'gilang.r@example.com', 'password' => bcrypt('123'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitTikId, 'jabatan_id' => $jabatanStaffId, 'nik' => '77706']);

            // 13. Staff Layanan 7 (Akademik)
            User::create(['name' => 'Hesti Wulandari', 'email' => 'hesti.w@example.com', 'password' => bcrypt('123'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitAkademikId, 'jabatan_id' => $jabatanStaffId, 'nik' => '77707']);

            // 14. Staff Layanan 8 (Kemahasiswaan)
            User::create(['name' => 'Indra Gunawan', 'email' => 'indra.g@example.com', 'password' => bcrypt('123'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitKemahasiswaanId, 'jabatan_id' => $jabatanStaffId, 'nik' => '77708']);

            // 15. Staff Layanan 9 (UPA TIK)
            User::create(['name' => 'Joko Susilo', 'email' => 'joko.s@example.com', 'password' => bcrypt('123'), 'role' => 'admin_unit'])
                ->staff()->create(['unit_id' => $unitTikId, 'jabatan_id' => $jabatanStaffId, 'nik' => '77709']);
        } catch (\Exception $e) {
            // Memberikan pesan error yang lebih jelas di konsol jika seeder gagal
            $this->command->error('Gagal menjalankan UserRoleSeeder: ' . $e->getMessage());
        }
    }
}