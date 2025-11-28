<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Notifications\ResetPasswordNotification;

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

  protected static function boot()
  {
    parent::boot();

    static::deleting(function ($user) {
      if ($user->avatar) {
        // Path di storage/app/public/avatar/
        $fullPath = 'avatar/' . $user->avatar;

        // Cek dan hapus file
        if (Storage::disk('public')->exists($fullPath)) {
          Storage::disk('public')->delete($fullPath);
        }
      }
    });
  }

  public function getAvatarUrlAttribute()
  {
    if ($this->avatar) {
      return asset('storage/avatar/' . $this->avatar);
    }

    $name = $this->name ?? 'Guest';
    $colors = ['7367f0', '28c76f', 'ea5455', 'ff9f43', '00cfe8'];
    $index = ord(strtolower($name[0])) % count($colors);
    $background = $colors[$index];

    return "https://ui-avatars.com/api/?name=" . urlencode($name) .
      "&background={$background}&color=fff&size=128&bold=true";
  }

  public function getProfilePhotoUrlAttribute()
  {
    return $this->getAvatarUrlAttribute();
  }

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
    // KOREKSI: Menentukan foreign key 'user_id' dan local key 'id'
    return $this->hasOne(Mahasiswa::class, 'user_id', 'id');
  }

  // RELASI: Satu User memiliki satu profil Staff (jika dia staff)
  public function staff()
  {
    // Asumsi foreign key di tabel staff juga 'user_id'
    return $this->hasOne(Staff::class, 'user_id', 'id');
  }

  // RELASI: User bisa membuat banyak tiket
  public function tiket()
  {
    // Asumsi foreign key di tabel tiket adalah 'user_id'
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
    // Asumsi foreign key di tabel artikel adalah 'user_id'
    return $this->hasMany(Artikel::class, 'user_id', 'id');
  }


  public function getInitialsAttribute()
  {
    $words = explode(' ', $this->name);

    if (count($words) >= 2) {
      return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    }

    return strtoupper(substr($this->name, 0, 2));
  }



  public function sendPasswordResetNotification($token)
  {
    $this->notify(new ResetPasswordNotification($token));
  }
}
