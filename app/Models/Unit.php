<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Unit extends Model
{
    protected $table = 'units';
    protected $guarded = ['id'];
    protected $fillable = ['nama_unit', 'slug'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($unit) {
            $unit->slug = $unit->createSlug($unit->nama_unit);
        });

        static::updating(function ($unit) {
            if ($unit->isDirty('nama_unit')) {
                $unit->slug = $unit->createSlug($unit->nama_unit);
            }
        });
    }

    private function createSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 2;

        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
    public function staff()
    {
        return $this->hasMany(Staff::class, 'unit_id');
    }
    public function kepalaUnit()
    {
        return $this->belongsTo(Staff::class, 'kepala_id');
    }
}
