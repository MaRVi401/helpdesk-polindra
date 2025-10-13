<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTiketSuratKetAktif extends Model
{
    use HasFactory;
    protected $table = 'detail_tiket_surat_ket_aktif';
    // PERBAIKAN: Menambahkan semua kolom yang dibutuhkan
    protected $fillable = ['tiket_id', 'keperluan', 'tahun_ajaran', 'semester', 'keperluan_lainnya'];

    public function tiket()
    {
        return $this->belongsTo(Tiket::class);
    }
}