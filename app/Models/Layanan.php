<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'layanan';

    protected $fillable = [
        'unit_id',
        'layanan',
        'deskripsi',
        'estimasi_waktu',
        'tipe_tiket'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function penanggungJawab()
    {
        return $this->belongsToMany(Staff::class, 'layanan_penanggung_jawab', 'layanan_id', 'staff_id');
    }
}
