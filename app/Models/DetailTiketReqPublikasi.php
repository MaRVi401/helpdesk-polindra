<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTiketReqPublikasi extends Model
{
    use HasFactory;
    protected $table = 'detail_tiket_req_publikasi';
    // PERBAIKAN: Menambahkan semua kolom yang dibutuhkan
    protected $fillable = ['tiket_id', 'judul', 'kategori', 'konten', 'gambar'];

    public function tiket()
    {
        return $this->belongsTo(Tiket::class);
    }
}