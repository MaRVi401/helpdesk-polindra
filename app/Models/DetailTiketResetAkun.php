<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetailTiketResetAkun extends Model
{
    protected $table = 'detail_tiket_reset_akun';
    protected $guarded = ['id'];

    public function tiket() { return $this->belongsTo(Tiket::class); }
}