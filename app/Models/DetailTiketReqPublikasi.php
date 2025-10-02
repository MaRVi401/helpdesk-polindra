<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetailTiketReqPublikasi extends Model
{
    protected $table = 'detail_tiket_req_publikasi';
    protected $guarded = ['id'];

    public function tiket() { return $this->belongsTo(Tiket::class); }
}