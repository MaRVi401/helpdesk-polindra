<?php

namespace Database\Seeders;

use App\Models\Tiket;
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
            UnitSeeder::class,
            PositionSeeder::class,
            UserRoleSeeder::class,
            MahasiswaSeeder::class,
            LayananSeeder::class,
            KategoriArtikelSeeder::class,
            StudentWithoutNimSeeder::class,
            TiketSeeder::class,
        ]);
    }
}