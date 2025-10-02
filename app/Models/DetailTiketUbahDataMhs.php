<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTiketUbahDataMhs extends Model
{
    protected $table = 'detail_tiket_ubah_data_mhs';
    
    protected $fillable = [
        'tiket_id',
        'data_nama_lengkap',
        'data_tmp_lahir',
        'data_tgl_lhr',
    ];

    public function tiket()
    {
        return $this->belongsTo(Tiket::class);
    }
}