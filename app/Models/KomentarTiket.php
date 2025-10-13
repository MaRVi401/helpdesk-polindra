<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomentarTiket extends Model
{
    use HasFactory;

    protected $table = 'komentar_tiket';

    
    protected $fillable = [
        'tiket_id',
        'pengirim_id',
        'komentar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    public function tiket()
    {
        return $this->belongsTo(Tiket::class, 'tiket_id');
    }
}