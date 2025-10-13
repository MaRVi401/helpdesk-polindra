<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'faq';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'layanan_id',
        'judul',
        'deskripsi',
        'status',
    ];

    /**
     * Mendapatkan user yang membuat FAQ.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan layanan yang terkait dengan FAQ.
     */
    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }
}