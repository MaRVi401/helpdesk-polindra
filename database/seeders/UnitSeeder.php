<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Matikan foreign key check untuk truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Unit::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Unit::create(['nama_unit' => 'UPA TIK']);
        Unit::create(['nama_unit' => 'Akademik']);
        Unit::create(['nama_unit' => 'Kemahasiswaan']);
        Unit::create(['nama_unit' => 'UPT. Bahasa']);
    }
}