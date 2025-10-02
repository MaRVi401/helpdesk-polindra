<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    protected $table = 'tiket';
    protected $guarded = ['id'];

    // RELASI PENTING: Menggunakan foreign key 'pemohon_id'
    public function pemohon()
    {
        return $this->belongsTo(User::class, 'pemohon_id');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }

    public function komentar()
    {
        return $this->hasMany(KomentarTiket::class);
    }

    public function jawaban()
    {
        return $this->belongsTo(KomentarTiket::class, 'jawaban_id');
    }

    // ... relasi ke detail tiket
}