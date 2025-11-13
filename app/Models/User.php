<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
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
    // KOREKSI: Menentukan foreign key 'id_user' dan local key 'id'
    return $this->hasOne(Mahasiswa::class, 'user_id', 'id');
  }

  // RELASI: Satu User memiliki satu profil Staff (jika dia staff)
  public function staff()
  {
    // Asumsi foreign key di tabel staff juga 'id_user'
    return $this->hasOne(Staff::class, 'user_id', 'id');
  }

  // RELASI: User bisa membuat banyak tiket
  public function tiket()
  {
    // Asumsi foreign key di tabel tiket adalah 'id_user'
    return $this->hasMany(Tiket::class, 'user_id', 'id');
  }

  // RELASI: User bisa mengirim banyak komentar
  public function komentarTiket()
  {
    return $this->hasMany(KomentarTiket::class, 'pengirim_id');
  }

  // RELASI: User bisa membuat banyak artikel
  public function artikel()
  {
    // Asumsi foreign key di tabel artikel adalah 'id_user'
    return $this->hasMany(Artikel::class, 'user_id', 'id');
  }

  public function getProfilePhotoUrlAttribute()
  {
    // Jika ada foto profile dari storage
    if ($this->profile_photo_path) {
      return asset('storage/' . $this->profile_photo_path);
    }

    // Jika tidak ada, generate dari nama
    $name = $this->name ?? 'Guest';
    $colors = ['7367f0', '28c76f', 'ea5455', 'ff9f43', '00cfe8'];
    $index = ord(strtolower($name[0])) % count($colors);
    $background = $colors[$index];

    return "https://ui-avatars.com/api/?name=" . urlencode($name) .
      "&background={$background}&color=fff&size=128&bold=true";
  }

  public function getInitialsAttribute()
  {
    $words = explode(' ', $this->name);

    if (count($words) >= 2) {
      return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    }

    return strtoupper(substr($this->name, 0, 2));
  }
  
}