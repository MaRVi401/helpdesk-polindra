<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriArtikel extends Model
{
    use HasFactory;
    
    protected $table = 'kategori_artikel';
    protected $guarded = ['id'];

    public function artikels()
    {
        return $this->hasMany(Artikel::class, 'kategori_id');
    }
}
