<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RiwayatStatusTiket extends Model
{
    protected $table = 'riwayat_status_tiket';
    protected $guarded = ['id'];

    public function tiket()
    {
        return $this->belongsTo(Tiket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}