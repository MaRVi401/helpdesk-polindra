<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MasterDataSeeder::class,
            UserRoleSeeder::class,
            MahasiswaSeeder::class,
            LayananSeeder::class,
            UnitSeeder::class,
            KategoriArtikelSeeder::class,
            ApiTestSeeder::class,
            StudentWithoutNimSeeder::class,
            // tiketResetAkunSeeder::class,
            // TiketUbahDataMhsSeeder::class,
            // tiketSuratKeteranganAktifKuliahSeeder::class,
            // tiketRequestPublikasiSeeder::class,
        ]);
    }
}