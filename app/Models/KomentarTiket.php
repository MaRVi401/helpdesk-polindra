<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KomentarTiket extends Model
{
    protected $table = 'komentar_tiket';
    protected $guarded = ['id'];

    public function tiket()
    {
        return $this->belongsTo(Tiket::class);
    }

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }
}