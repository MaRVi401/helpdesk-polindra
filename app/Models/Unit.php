<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'units';
    protected $guarded = ['id'];
    protected $fillable = ['nama_unit', 'slug'];

    public function staff()
    {
        return $this->hasMany(Staff::class, 'unit_id');
    }
    public function kepalaUnit()
    {
        return $this->belongsTo(Staff::class, 'kepala_id');
    }
}