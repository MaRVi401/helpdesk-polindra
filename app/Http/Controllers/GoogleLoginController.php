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

    // app/Http/Controllers/GoogleLoginController.php

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                Auth::login($user);
                return redirect()->intended('dashboard');
            }

            // Definisikan variabel untuk menampung user baru di luar transaksi
            $newUser = null;

            // Jalankan transaksi HANYA untuk membuat data
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

                // Simpan user yang baru dibuat ke variabel
                $newUser = $createdUser;
            });

            // Lakukan login SETELAH transaksi database selesai
            if ($newUser) {
                Auth::login($newUser);
            }

            return redirect()->intended('dashboard');
        } catch (Exception $e) {
            dd($e);
        }
    }
}
