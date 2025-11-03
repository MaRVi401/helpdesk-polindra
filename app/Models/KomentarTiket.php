<?php

namespace App\Models;

use App\Models\User;
use App\Models\Tiket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KomentarTiket extends Model
{
    use HasFactory;

    protected $table = 'komentar_tiket';

    protected $fillable = [
        'tiket_id',
        'pengirim_id',
        'komentar',
    ];

    /**
     * Relasi ke User (pengirim)
     */
    public function pengirim()
    {
        // Menghubungkan 'pengirim_id' di tabel ini ke 'id' di tabel 'users'
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    /**
     * Relasi ke Tiket
     */
    public function tiket()
    {
        return $this->belongsTo(Tiket::class, 'tiket_id');
    }
}