<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'layanan';
    protected $guarded = ['id'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function penanggungJawab()
    {
        return $this->belongsToMany(Staff::class, 'layanan_penanggung_jawab');
    }
}