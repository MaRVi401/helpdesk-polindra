<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mahasiswa;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            // Mengambil data user dari Google
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            // Cari user berdasarkan email. Jika tidak ada, buat baru.
            $user = User::firstOrCreate(
                [
                    'email' => $userEmail,
                ],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role' => 'mahasiswa',
                    'password' => null,
                ]
            );

            // Login the found or newly created user
            Auth::login($user);

            $mahasiswaExists = Mahasiswa::where('user_id', $user->id)->exists();

            if ($mahasiswaExists) {
                return redirect()->intended('dashboard');
            } else {
                session(['needs_profile_completion' => true]);
                return redirect('/lengkapi-profil');
            }

        } catch (Exception $e) {
            // Jika terjadi error, kembali ke halaman login dengan pesan error
            return redirect('/login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }
}
