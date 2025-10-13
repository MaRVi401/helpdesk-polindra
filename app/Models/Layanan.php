<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;
    
    
    protected $table = 'layanan';

    protected $fillable = [
        'nama',
        'status_arsip',
        'unit_id',
        'prioritas',
    ];

    
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function tikets()
    {
        return $this->hasMany(Tiket::class, 'layanan_id');
    }

    public function penanggungJawab()
    {
        return $this->belongsToMany(Staff::class, 'layanan_penanggung_jawab', 'layanan_id', 'staff_id');
    }
}