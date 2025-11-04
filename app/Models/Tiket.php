<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tiket extends Model
{
    use HasFactory;

    protected $table = 'tiket';
    protected $fillable = [
        'no_tiket',
        'pemohon_id',
        'layanan_id',
        'deskripsi',
        'jawaban_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relasi ke Layanan
    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    // Relasi ke Pemohon (User)
    public function pemohon()
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }

    // Relasi ke Riwayat Status
    public function riwayatStatus()
    {
        // Selalu urutkan dari yang terbaru
        return $this->hasMany(RiwayatStatusTiket::class)->orderBy('created_at', 'desc');
    }

    /**
     * Relasi HANYA ke status TERBARU.
     * Ini penting untuk menampilkan status tiket saat ini di halaman index.
     */
    public function statusTerbaru()
    {
        // Mengambil satu relasi RiwayatStatusTiket yang paling baru
        return $this->hasOne(RiwayatStatusTiket::class)->latestOfMany();
    }

    // Relasi ke Komentar
    public function komentar()
    {
        // Selalu urutkan dari yang terlama ke terbaru untuk dibaca
        return $this->hasMany(KomentarTiket::class)->orderBy('created_at', 'asc');
    }

    // Relasi ke Jawaban (KomentarTiket)
    public function jawaban()
    {
        return $this->belongsTo(KomentarTiket::class, 'jawaban_id');
    }

    // Relasi ke Detail Tiket Reset Akun
    public function detailResetAkun()
    {
        return $this->hasOne(DetailTiketResetAkun::class);
    }

    // Relasi ke Detail Tiket Surat Keterangan Aktif
    public function detailSuratKetAktif()
    {
        return $this->hasOne(DetailTiketSuratKetAktif::class);
    }

    // Relasi ke Detail Tiket Ubah Data Mahasiswa
    public function detailUbahDataMhs()
    {
        return $this->hasOne(DetailTiketUbahDataMhs::class);
    }

    // Relasi ke Detail Tiket Request Publikasi
    public function detailReqPublikasi()
    {
        return $this->hasOne(DetailTiketReqPublikasi::class);
    }
}
