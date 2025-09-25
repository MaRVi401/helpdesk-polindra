<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Mahasiswa;
use Exception;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                Auth::login($user);
                return redirect()->intended('dashboard');
            }

            DB::transaction(function () use ($googleUser) {
                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role' => 'mahasiswa',
                    'password' => null,
                ]);

                Mahasiswa::create(['user_id' => $newUser->id]);

                Auth::login($newUser);
            });

            return redirect()->intended('dashboard');
        } catch (Exception $e) {
            return redirect('/login')->withErrors(['msg' => 'Login Google Gagal!']);
        }
    }
}
