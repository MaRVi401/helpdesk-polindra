<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mahasiswa;
use Exception;
use Illuminate\Support\Facades\Auth;
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

            // Search for users by email. If it doesn't exist, create a new one.
            $user = User::firstOrCreate(
                [
                    'email' => $googleUser->getEmail(),
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
            // If an error occurs, return to the login page with an error message.
            return redirect('/login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }
}