<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    /**
     *
     * @var string
     */
    protected $table = 'mahasiswa';

    /**
     * 
     * @var array
     */
    protected $fillable = [
        'user_id',
        'program_studi_id',
        'nim',
        'tahun_masuk',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class);
    }
}