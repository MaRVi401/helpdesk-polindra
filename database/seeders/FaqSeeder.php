<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Faq::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $faqs = [
            [
                'user_id' => 1, // Asumsi User Pembuat FAQ
                'layanan_id' => 1, // Asumsi ID layanan yang terkait
                'judul' => 'Bagaimana cara mengajukan tiket baru?',
                'deskripsi' => 'Pengajuan tiket baru dilakukan melalui halaman "Layanan". Pilih jenis layanan yang Anda butuhkan, lengkapi formulir yang tersedia, dan kirimkan.',
                'status' => 'Post',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_id' => 1,
                'layanan_id' => 2,
                'judul' => 'Berapa lama waktu yang dibutuhkan untuk menyelesaikan tiket?',
                'deskripsi' => 'Waktu penyelesaian tergantung pada prioritas dan jenis layanan yang diajukan. Kami akan memberikan estimasi waktu di setiap balasan tiket pertama.',
                'status' => 'Post',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'user_id' => 1,
                'layanan_id' => 3,
                'judul' => 'Apakah saya bisa melampirkan file saat membuat tiket?',
                'deskripsi' => 'Ya, Anda dapat melampirkan file pendukung seperti screenshot atau dokumen relevan saat membuat tiket di bagian "Lampiran".',
                'status' => 'Post',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => 1,
                'layanan_id' => 4,
                'judul' => 'Bagaimana cara mengecek status tiket?',
                'deskripsi' => 'Status tiket dapat dilihat melalui halaman "Riwayat Tiket", lengkap dengan waktu pembaruan terbaru.',
                'status' => 'Post',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],
        ];

        Faq::insert($faqs);
    }
}
