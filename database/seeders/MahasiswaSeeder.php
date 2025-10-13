<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID dari program studi yang sudah ada di MasterDataSeeder
        $prodiTI = ProgramStudi::where('program_studi', 'D4 - Teknik Informatika')->first();
        $prodiTM = ProgramStudi::where('program_studi', 'D4 - Teknik Mesin')->first();
        $prodiKP = ProgramStudi::where('program_studi', 'D3 - Keperawatan')->first();

        // Daftar nama mahasiswa untuk dibuat (disesuaikan dengan prodi yang ada)
        $mahasiswas = [
            // Mahasiswa Teknik Informatika
            ['name' => 'Ahmad Fauzi', 'nim' => '2203001', 'prodi' => $prodiTI, 'tahun' => '2022'],
            ['name' => 'Dewi Anjani', 'nim' => '2103004', 'prodi' => $prodiTI, 'tahun' => '2021'],
            ['name' => 'Gunawan', 'nim' => '2003007', 'prodi' => $prodiTI, 'tahun' => '2020'],
            ['name' => 'Joko Susanto', 'nim' => '2303010', 'prodi' => $prodiTI, 'tahun' => '2023'],
            ['name' => 'Muhammad Ali', 'nim' => '2203013', 'prodi' => $prodiTI, 'tahun' => '2022'],

            // Mahasiswa Teknik Mesin
            ['name' => 'Candra Darmawan', 'nim' => '2203003', 'prodi' => $prodiTM, 'tahun' => '2022'],
            ['name' => 'Fitriani', 'nim' => '2103006', 'prodi' => $prodiTM, 'tahun' => '2021'],
            ['name' => 'Indra Saputra', 'nim' => '2003009', 'prodi' => $prodiTM, 'tahun' => '2020'],
            ['name' => 'Lestari', 'nim' => '2303012', 'prodi' => $prodiTM, 'tahun' => '2023'],
            ['name' => 'Putri Ayu', 'nim' => '2203015', 'prodi' => $prodiTM, 'tahun' => '2022'],
            
            // Mahasiswa Keperawatan
            ['name' => 'Bunga Citra', 'nim' => '2203002', 'prodi' => $prodiKP, 'tahun' => '2022'],
            ['name' => 'Eko Prasetyo', 'nim' => '2103005', 'prodi' => $prodiKP, 'tahun' => '2021'],
            ['name' => 'Herlina', 'nim' => '2003008', 'prodi' => $prodiKP, 'tahun' => '2020'],
            ['name' => 'Kartika', 'nim' => '2303011', 'prodi' => $prodiKP, 'tahun' => '2023'],
            ['name' => 'Nurhayati', 'nim' => '2203014', 'prodi' => $prodiKP, 'tahun' => '2022'],
        ];

        foreach ($mahasiswas as $mhs) {
            // Membuat email dari nama (lower case, ganti spasi dengan titik)
            $emailName = strtolower(str_replace(' ', '.', $mhs['name']));
            $email = $emailName . '@student.polindra.ac.id';

            // Cek jika prodi ada, jika tidak lewati
            if (!$mhs['prodi']) {
                $this->command->warn("Program studi untuk {$mhs['name']} tidak ditemukan, data dilewati.");
                continue;
            }

            // Buat user baru dengan role 'mahasiswa'
            $user = User::create([
                'name' => $mhs['name'],
                'email' => $email,
                'password' => bcrypt('123'), // Password default
                'role' => 'mahasiswa',
            ]);

            // Buat data mahasiswa yang terhubung dengan user
            $user->mahasiswa()->create([
                'program_studi_id' => $mhs['prodi']->id,
                'nim' => $mhs['nim'],
                'tahun_masuk' => $mhs['tahun'],
            ]);
        }
    }
}
