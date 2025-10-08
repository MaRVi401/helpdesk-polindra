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
        // Ambil ID dari program studi yang sudah ada
        $prodiTI = ProgramStudi::where('program_studi', 'D3 - Teknik Informatika')->first();
        $prodiRPL = ProgramStudi::where('program_studi', 'D4 - Teknik Perangkat Lunak')->first();
        $prodiTM = ProgramStudi::where('program_studi', 'D3 - Teknik Mesin')->first();

        // Daftar nama mahasiswa untuk dibuat
        $mahasiswas = [
            ['name' => 'Ahmad Fauzi', 'nim' => '2203001', 'prodi' => $prodiTI, 'tahun' => '2022'],
            ['name' => 'Bunga Citra', 'nim' => '2203002', 'prodi' => $prodiRPL, 'tahun' => '2022'],
            ['name' => 'Candra Darmawan', 'nim' => '2203003', 'prodi' => $prodiTM, 'tahun' => '2022'],
            ['name' => 'Dewi Anjani', 'nim' => '2103004', 'prodi' => $prodiTI, 'tahun' => '2021'],
            ['name' => 'Eko Prasetyo', 'nim' => '2103005', 'prodi' => $prodiRPL, 'tahun' => '2021'],
            ['name' => 'Fitriani', 'nim' => '2103006', 'prodi' => $prodiTM, 'tahun' => '2021'],
            ['name' => 'Gunawan', 'nim' => '2003007', 'prodi' => $prodiTI, 'tahun' => '2020'],
            ['name' => 'Herlina', 'nim' => '2003008', 'prodi' => $prodiRPL, 'tahun' => '2020'],
            ['name' => 'Indra Saputra', 'nim' => '2003009', 'prodi' => $prodiTM, 'tahun' => '2020'],
            ['name' => 'Joko Susanto', 'nim' => '2303010', 'prodi' => $prodiTI, 'tahun' => '2023'],
            ['name' => 'Kartika', 'nim' => '2303011', 'prodi' => $prodiRPL, 'tahun' => '2023'],
            ['name' => 'Lestari', 'nim' => '2303012', 'prodi' => $prodiTM, 'tahun' => '2023'],
            ['name' => 'Muhammad Ali', 'nim' => '2203013', 'prodi' => $prodiTI, 'tahun' => '2022'],
            ['name' => 'Nurhayati', 'nim' => '2203014', 'prodi' => $prodiRPL, 'tahun' => '2022'],
            ['name' => 'Putri Ayu', 'nim' => '2203015', 'prodi' => $prodiTM, 'tahun' => '2022'],
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