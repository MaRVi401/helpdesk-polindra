<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\Jurusan;
use App\Models\ProgramStudi;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mengosongkan tabel dengan aman sebelum mengisi data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Jabatan::truncate();
        Unit::truncate();
        ProgramStudi::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Membuat data Jabatan
        Jabatan::create(['nama_jabatan' => 'Super Administrator']);
        Jabatan::create(['nama_jabatan' => 'Kepala Unit']);
        Jabatan::create(['nama_jabatan' => 'Staff Layanan']);

        // Membuat data Unit sesuai permintaan
        Unit::create(['nama_unit' => 'UPA TIK']);
        Unit::create(['nama_unit' => 'Akademik']);
        Unit::create(['nama_unit' => 'Kemahasiswaan']);

        $jurusanTI = Jurusan::create(['nama_jurusan' => 'Teknik Informatika']);
        $jurusanTM = Jurusan::create(['nama_jurusan' => 'Teknik Mesin']);
        $jurusanKP = Jurusan::create(['nama_jurusan' => 'Keperawatan']);

        // Membuat data Program Studi
        ProgramStudi::create([
            'program_studi' => 'D4 - Teknik Informatika',
            'jurusan_id' => $jurusanTI->id,
        ]);
        ProgramStudi::create([
            'program_studi' => 'D4 - Teknik Mesin',
            'jurusan_id' => $jurusanTM->id, 
        ]);
        ProgramStudi::create([
            'program_studi' => 'D3 - Keperawatan',
            'jurusan_id' => $jurusanKP->id, 
        ]);

        // $this->command->info('MasterDataSeeder berhasil dijalankan.');
    }
}
