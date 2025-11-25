<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model

{
    use HasFactory;
    protected $table = 'units';
    protected $guarded = ['id'];
    protected $fillable = ['nama_unit', 'slug', 'kepala_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($unit) {

            if (empty($unit->slug)) {
                $unit->slug = $unit->createSlug($unit->nama_unit);
            } else {
                $unit->slug = $unit->createSlug($unit->slug);
            }
        });

        static::updating(function ($unit) {

            if (empty($unit->slug)) {
                $unit->slug = $unit->createSlug($unit->nama_unit);
            }
            elseif ($unit->isDirty('slug')) {
                $unit->slug = $unit->createSlug($unit->slug);
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
