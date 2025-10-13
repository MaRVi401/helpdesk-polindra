<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTiketResetAkun extends Model
{
    use HasFactory;
    protected $table = 'detail_tiket_reset_akun';
    // PERBAIKAN: Menambahkan semua kolom yang dibutuhkan
    protected $fillable = ['tiket_id', 'aplikasi', 'deskripsi'];

    public function tiket()
    {
        return $this->belongsTo(Tiket::class);
    }
}