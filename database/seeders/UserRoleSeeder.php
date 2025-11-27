<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Staff;
use App\Models\Jabatan;
use App\Models\Unit;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        try {
            // Ambil ID Jabatan
            $jabatanSUA = Jabatan::where('nama_jabatan', 'Super Administrator')->firstOrFail()->id;
            $jabatanKBU = Jabatan::where('nama_jabatan', 'Kepala Bagian Umum')->firstOrFail()->id;
            $jabatanKBA = Jabatan::where('nama_jabatan', 'Kepala Bagian Akademik')->firstOrFail()->id;
            $jabatanPK = Jabatan::where('nama_jabatan', 'Ahli Pertama Pranata Komputer')->firstOrFail()->id;
            $jabatanHumas = Jabatan::where('nama_jabatan', 'Ahli Pranata Humas')->firstOrFail()->id;

            // Ambil ID Unit
            $unitTikId = Unit::where('nama_unit', 'UPA TIK')->firstOrFail()->id;
            $unitAkademikId = Unit::where('nama_unit', 'Akademik')->firstOrFail()->id;
            $unitKemahasiswaanId = Unit::where('nama_unit', 'Kemahasiswaan')->firstOrFail()->id;
            $unitUptBahasaId = Unit::where('nama_unit', 'UPT. Bahasa')->firstOrFail()->id;

            /* ===========================
             * 1. SUPER ADMIN → UPA TIK
            ============================*/
            $superAdmin = User::firstOrCreate(
                ['email' => 'superadmin@email.com'],
                ['name' => 'Super Administrator', 'password' => bcrypt('12345678'), 'role' => 'super_admin']
            );

            $superAdmin->staff()->updateOrCreate(
                ['user_id' => $superAdmin->id],
                [
                    'unit_id' => $unitTikId,
                    'jabatan_id' => $jabatanSUA,
                    'nik' => '3201010101000001'
                ]
            );


            /* ===========================
             * 2. KEPALA UNIT → UPA TIK
            ============================*/
            $kepalaUnitUser = User::firstOrCreate(
                ['email' => 'adisuheryadi@email.com'],
                ['name' => 'Adi Suheryadi', 'password' => bcrypt('12345678'), 'role' => 'kepala_unit']
            );

            $kepalaUnitStaff = Staff::updateOrCreate(
                ['user_id' => $kepalaUnitUser->id],
                [
                    'unit_id' => $unitTikId,
                    'jabatan_id' => $jabatanKBA,
                    'nik' => '3201010202000002'
                ]
            );

            Unit::find($unitTikId)->update(['kepala_id' => $kepalaUnitStaff->id]);


            /* ===========================
             * 3. ADMIN AKADEMIK
            ============================*/
            $adminAkademik = User::firstOrCreate(
                ['email' => 'fachrizal@email.com'],
                ['name' => 'Fachrizal Bachar', 'password' => bcrypt('12345678'), 'role' => 'admin_unit']
            );

            $adminAkademik->staff()->updateOrCreate(
                ['user_id' => $adminAkademik->id],
                [
                    'unit_id' => $unitAkademikId,
                    'jabatan_id' => $jabatanPK,
                    'nik' => '3201010303000003'
                ]
            );


            /* ===========================
             * 4. ADMIN KEMAHASISWAAN
            ============================*/
            $kemahasiswaanUser = User::firstOrCreate(
                ['email' => 'citra@email.com'],
                ['name' => 'Bunga Citra Lestari', 'password' => bcrypt('12345678'), 'role' => 'admin_unit']
            );

            $kemahasiswaanUser->staff()->updateOrCreate(
                ['user_id' => $kemahasiswaanUser->id],
                [
                    'unit_id' => $unitKemahasiswaanId,
                    'jabatan_id' => $jabatanHumas,
                    'nik' => '3201010808000008'
                ]
            );


            /* ===========================
             * 5. ADMIN UPT BAHASA
            ============================*/
            $febri = User::firstOrCreate(
                ['email' => 'febri@example.com'],
                ['name' => 'Febri Mulyadi', 'password' => bcrypt('12345678'), 'role' => 'admin_unit']
            );

            $febri->staff()->updateOrCreate(
                ['user_id' => $febri->id],
                [
                    'unit_id' => $unitUptBahasaId,
                    'jabatan_id' => $jabatanHumas,
                    'nik' => '3201010909000009'
                ]
            );

        } catch (\Exception $e) {
            $this->command->error('Gagal menjalankan UserRoleSeeder: ' . $e->getMessage());
        }
    }
}