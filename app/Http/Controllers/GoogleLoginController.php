<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mahasiswa;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    /**
     * Handle the Google callback, verify the email domain, and log in the user.
     */
    public function handleGoogleCallback()
    {
        try {
            // Mengambil data user dari Google
            $googleUser = Socialite::driver('google')->stateless()->user();
            $userEmail = $googleUser->getEmail();
            $allowedDomain = '@student.polindra.ac.id';

            // --- VALIDASI DOMAIN EMAIL ---
            // Cek apakah - email user diakhiri dengan domain yang diizinkan
            if (!str_ends_with($userEmail, $allowedDomain)) {
                // Jika tidak sesuai, arahkan kembali ke login dengan pesan error
                return redirect('/login')->with('error', 'Email anda tidak terdata dalam data kampus');
            }

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

            // Login user yang ditemukan atau yang baru dibuat
            Auth::login($user, true);

            // Periksa apakah user ini sudah memiliki data profil mahasiswa
            $mahasiswaExists = Mahasiswa::where('user_id', $user->id)->exists();

            if ($mahasiswaExists) {
                return redirect()->intended('dashboard');
            } else {
                session(['needs_profile_completion' => true]);
                return redirect('/lengkapi-profil');
            }

        } catch (Exception $e) {
            // Jika terjadi error, kembali ke halaman login dengan pesan error.
            return redirect('/login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }
}