<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    use HasFactory;

    protected $table = 'tiket';

    // PERBAIKAN: Disesuaikan 100% dengan file migrasi
    protected $fillable = [
        'no_tiket',
        'pemohon_id',
        'layanan_id',
        'deskripsi',
        'jawaban_id',
    ];

    public function user() { return $this->belongsTo(User::class, 'pemohon_id'); }
    public function layanan() { return $this->belongsTo(Layanan::class, 'layanan_id'); }
    public function komentars() { return $this->hasMany(KomentarTiket::class, 'tiket_id'); }
    public function riwayatStatus() { return $this->hasMany(RiwayatStatusTiket::class, 'tiket_id'); }
    
    // Relasi One-to-One ke semua tabel Detail
    public function detailUbahData() { return $this->hasOne(DetailTiketUbahDataMhs::class, 'tiket_id'); }
    public function detailReset() { return $this->hasOne(DetailTiketResetAkun::class, 'tiket_id'); }
    public function detailSurat() { return $this->hasOne(DetailTiketSuratKetAktif::class, 'tiket_id'); }
    public function detailPublikasi() { return $this->hasOne(DetailTiketReqPublikasi::class, 'tiket_id'); }
}