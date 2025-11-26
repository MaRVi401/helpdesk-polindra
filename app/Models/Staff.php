<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;
    
    // Menentukan nama tabel secara eksplisit
    protected $table = 'staff';

    protected $fillable = [
        'user_id',
        'unit_id',
        'jabatan_id',
        'nik',
    ];

    // RELASI: Staff dimiliki oleh satu User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // RELASI: Staff berada di satu Unit
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // RELASI: Staff memiliki satu Jabatan
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    // RELASI: Staff bisa menjadi penanggung jawab banyak Layanan (Many-to-Many)
    public function layanan()
    {
        return $this->belongsToMany(Layanan::class, 'layanan_penanggung_jawab');
    }
}