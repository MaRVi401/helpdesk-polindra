<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LayananPenanggungJawab extends Model
{
    use HasFactory;

    // Nama tabel sesuai skema migrasi
    protected $table = 'layanan_penanggung_jawab';
    
    // Non-fillable fields
    protected $guarded = ['id'];
    
    // Relasi
    
    /**
     * Layanan yang dimiliki oleh Penanggung Jawab ini.
     */
    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }

    /**
     * Staff yang menjadi Penanggung Jawab.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}