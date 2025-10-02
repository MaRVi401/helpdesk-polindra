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
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = User::where('email', $googleUser->getEmail())->first();
            if ($user) {
                Auth::login($user);
                return redirect()->intended('dashboard');
            }

            // Define a variable to hold new users outside of transactions
            $newUser = null;

            // Execute transaction only to create data
            DB::transaction(function () use ($googleUser, &$newUser) {
                $createdUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role' => 'mahasiswa',
                    'password' => null,
                ]);

                Mahasiswa::create([
                    'user_id' => $createdUser->id,
                    'nim' => null
                ]);
                $newUser = $createdUser;
            });
            // Login after the database transaction is complete
            if ($newUser) {
                Auth::login($newUser);
            }
            return redirect()->intended('dashboard');
        } catch (Exception $e) {
            dd($e);
        }
    }
}
