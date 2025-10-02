<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mahasiswa; // Pastikan Mahasiswa di-import
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            // Cari user berdasarkan email. Jika tidak ada, buat baru.
            $user = User::firstOrCreate(
                [
                    'email' => $googleUser->getEmail(),
                ],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role' => 'mahasiswa',
                    'password' => null, // Password tidak diperlukan untuk OAuth
                ]
            );

            // Login user yang ditemukan atau yang baru dibuat
            Auth::login($user);

            // Periksa apakah user ini sudah memiliki data profil mahasiswa
            $mahasiswaExists = Mahasiswa::where('user_id', $user->id)->exists();

            if ($mahasiswaExists) {
                // Jika sudah ada, arahkan ke dashboard
                return redirect()->intended('dashboard');
            } else {
                // JIKA PENGGUNA BARU: Simpan penanda di session
                session(['needs_profile_completion' => true]);
                
                // dan arahkan ke halaman untuk melengkapi profil
                return redirect('/lengkapi-profil');
            }

        } catch (Exception $e) {
            // Jika terjadi error, kembali ke halaman login dengan pesan error
            return redirect('/login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }
}