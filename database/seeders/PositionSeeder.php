<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatan = [
            ['nama_jabatan' => 'Kepala Bagian Umum'],
            ['nama_jabatan' => 'Kepala Bagian Akademik'],
            ['nama_jabatan' => 'Ahli Pertama Pranata Komputer'],
            ['nama_jabatan' => 'Ahli Pranata Humas'],
        ];

        Jabatan::insert($jabatan);
    }
}
