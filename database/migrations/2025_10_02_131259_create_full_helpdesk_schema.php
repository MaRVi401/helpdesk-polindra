<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jurusan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jurusan');
            $table->timestamps();
        });

        Schema::create('program_studi', function (Blueprint $table) {
            $table->id();
            $table->string('program_studi');
            $table->foreignId('jurusan_id')->constrained('jurusan')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('jabatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jabatan');
            $table->timestamps();
        });

        Schema::create('kategori_artikel', function (Blueprint $table) {
            $table->id();
            $table->string('kategori');
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('nama_unit');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('kepala_id')->nullable();
            $table->timestamps();
        });

        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('program_studi_id')->constrained('program_studi')->nullable();
            $table->string('nim')->unique()->nullable();
            $table->year('tahun_masuk')->nullable();
            $table->timestamps();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('units');
            $table->foreignId('jabatan_id')->constrained('jabatan');
            $table->string('nik')->unique();
            $table->timestamps();
        });

        Schema::table('units', function (Blueprint $table) {
            $table->foreign('kepala_id')->references('id')->on('staff')->onDelete('set null');
        });

        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->boolean('status_arsip')->default(false);
            $table->foreignId('unit_id')->constrained('units');
            $table->integer('prioritas')->default(0);
            $table->timestamps();
        });

        Schema::create('layanan_penanggung_jawab', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_id')->constrained('layanan')->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('faq', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('layanan_id')->constrained('layanan');
            $table->string('judul');
            $table->text('deskripsi');
            $table->enum('status', ['Draft', 'Post'])->default('Draft');
            $table->timestamps();
        });

        Schema::create('artikel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('kategori_id')->constrained('kategori_artikel');
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('gambar')->nullable();
            $table->enum('status', ['Draft', 'Post'])->default('Draft');
            $table->timestamps();
        });

        Schema::create('tiket', function (Blueprint $table) {
            $table->id();
            $table->string('no_tiket')->unique();
            $table->foreignId('pemohon_id')->constrained('users');
            $table->foreignId('layanan_id')->constrained('layanan');
            $table->text('deskripsi');
            $table->unsignedBigInteger('jawaban_id')->nullable();
            $table->timestamps();
        });

        Schema::create('komentar_tiket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_id')->constrained('tiket')->onDelete('cascade');
            $table->foreignId('pengirim_id')->constrained('users');
            $table->text('komentar');
            $table->timestamps();
        });

        Schema::table('tiket', function (Blueprint $table) {
            $table->foreign('jawaban_id')->references('id')->on('komentar_tiket')->onDelete('set null');
        });

        Schema::create('riwayat_status_tiket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_id')->constrained('tiket')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('status', [
                'Diajukan_oleh_Pemohon',
                'Ditangani_oleh_PIC',
                'Diselesaikan_oleh_PIC',
                'Dinilai_Belum_Selesai_oleh_Pemohon',
                'Pemohon_Bermasalah',
                'Dinilai_Selesai_oleh_Kepala',
                'Dinilai_Selesai_oleh_Pemohon',
            ])->default('Diajukan_oleh_Pemohon');
            $table->timestamps();
        });

        Schema::create('detail_tiket_surat_ket_aktif', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_id')->constrained('tiket')->onDelete('cascade');
            $table->string('keperluan');
            $table->year('tahun_ajaran');
            $table->integer('semester');
            $table->string('keperluan_lainnya')->nullable();
            $table->timestamps();
        });

        Schema::create('detail_tiket_reset_akun', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_id')->constrained('tiket')->onDelete('cascade');
            $table->enum('aplikasi', ['gmail', 'office', 'sevima']);
            $table->text('deskripsi');
            $table->timestamps();
        });

        Schema::create('detail_tiket_ubah_data_mhs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_id')->constrained('tiket')->onDelete('cascade');
            $table->string('data_nama_lengkap');
            $table->string('data_tmp_lahir');
            $table->string('data_tgl_lhr');
            $table->timestamps();
        });

        Schema::create('detail_tiket_req_publikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_id')->constrained('tiket')->onDelete('cascade');
            $table->string('judul');
            $table->string('kategori');
            $table->text('konten');
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_tiket_req_publikasi');
        Schema::dropIfExists('detail_tiket_ubah_data_mhs');
        Schema::dropIfExists('detail_tiket_reset_akun');
        Schema::dropIfExists('detail_tiket_surat_ket_aktif');
        Schema::dropIfExists('riwayat_status_tiket');
        Schema::dropIfExists('komentar_tiket');
        Schema::dropIfExists('tiket');
        Schema::dropIfExists('artikel');
        Schema::dropIfExists('faq');
        Schema::dropIfExists('layanan_penanggung_jawab');
        Schema::dropIfExists('layanan');
        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['kepala_id']);
        });
        Schema::dropIfExists('staff');
        Schema::dropIfExists('mahasiswa');
        Schema::dropIfExists('units');
        Schema::dropIfExists('kategori_artikel');
        Schema::dropIfExists('jabatan');
        Schema::dropIfExists('program_studi');
        Schema::dropIfExists('jurusan');
    }
};
