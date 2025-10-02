<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\ProgramStudi;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat data Jabatan
        Jabatan::create(['nama_jabatan' => 'Super Administrator']);
        Jabatan::create(['nama_jabatan' => 'Kepala Unit']);
        Jabatan::create(['nama_jabatan' => 'Staff Layanan']);

        // Membuat data Unit
        Unit::create(['nama_unit' => 'UPT TIK']);
        Unit::create(['nama_unit' => 'BAAK']);

        // Membuat data Program Studi
        ProgramStudi::create(['program_studi' => 'D3 - Teknik Informatika', 'jurusan_id' => 'TI']);
        ProgramStudi::create(['program_studi' => 'D4 - Teknik Perangkat Lunak', 'jurusan_id' => 'TI']);
        ProgramStudi::create(['program_studi' => 'D3 - Teknik Mesin', 'jurusan_id' => 'TM']);
    }
}