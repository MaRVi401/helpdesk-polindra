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

        $jurusanTeknikInformatika = Jurusan::create(['nama_jurusan' => 'Teknik Informatika']);
        $jurusanTeknik = Jurusan::create(['nama_jurusan' => 'Teknik']);
        $jurusanKesehatan = Jurusan::create(['nama_jurusan' => 'Kesehatan']);

        // Membuat data Program Studi
        ProgramStudi::create([
            'program_studi' => 'D4 - Rekayasa Perangkat Lunak',
            'jurusan_id' => $jurusanTeknikInformatika->id,
        ]);
        ProgramStudi::create([
            'program_studi' => 'D4 - Sistem Informasi Kota Cerdas',
            'jurusan_id' => $jurusanTeknikInformatika->id,
        ]);
        ProgramStudi::create([
            'program_studi' => 'D4 - Teknologi Rekayasa Komputer',
            'jurusan_id' => $jurusanTeknikInformatika->id,
        ]);
        ProgramStudi::create([
            'program_studi' => 'D3 - Teknik Informatika',
            'jurusan_id' => $jurusanTeknikInformatika->id,
        ]);
        ProgramStudi::create([
            'program_studi' => 'D4 - Perancangan Manufaktur',
            'jurusan_id' => $jurusanTeknik->id,
        ]);
        ProgramStudi::create([
            'program_studi' => 'D4 - Teknologi Rekayasa Instrumen & Kontrol',
            'jurusan_id' => $jurusanTeknik->id,
        ]);
        ProgramStudi::create([
            'program_studi' => 'D3 - Teknik Mesin',
            'jurusan_id' => $jurusanTeknik->id,
        ]);
        ProgramStudi::create([
            'program_studi' => 'D3 - Pendingin & Tata Udara',
            'jurusan_id' => $jurusanTeknik->id,
        ]);
        ProgramStudi::create([
            'program_studi' => 'D4 - Teknologi Laboratorium Medis',
            'jurusan_id' => $jurusanKesehatan->id,
        ]);
        ProgramStudi::create([
            'program_studi' => 'D4 - Teknologi Rekayasa Elektro Medis',
            'jurusan_id' => $jurusanKesehatan->id,
        ]);
        ProgramStudi::create([
            'program_studi' => 'D3 - Keperawatan',
            'jurusan_id' => $jurusanKesehatan->id,
        ]);

        // $this->command->info('MasterDataSeeder berhasil dijalankan.');
    }
}