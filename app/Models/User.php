<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_personal',
        'no_wa',
        'google_id',
        'avatar',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // RELASI: Satu User memiliki satu profil Mahasiswa (jika dia mahasiswa)
    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class);
    }

    // RELASI: Satu User memiliki satu profil Staff (jika dia staff)
    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    // RELASI: User bisa membuat banyak tiket
    public function tiket()
    {
        return $this->hasMany(Tiket::class);
    }

    // RELASI: User bisa mengirim banyak komentar
    public function komentarTiket()
    {
        return $this->hasMany(KomentarTiket::class, 'pengirim_id');
    }

    // RELASI: User bisa membuat banyak artikel
    public function artikel()
    {
        return $this->hasMany(Artikel::class);
    }
}