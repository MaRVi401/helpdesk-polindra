<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetailTiketSuratKetAktif extends Model
{
    protected $table = 'detail_tiket_surat_ket_aktif';
    protected $guarded = ['id'];
    
    public function tiket() { return $this->belongsTo(Tiket::class); }
}