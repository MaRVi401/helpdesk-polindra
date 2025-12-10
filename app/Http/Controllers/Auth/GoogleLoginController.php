<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        // Tambahkan parameter untuk selalu menampilkan pemilihan akun
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    // Handle Google callback, verifikasi domain email, dan masuk sebagai pengguna
    public function handleGoogleCallback()
    {
        DB::beginTransaction();

        try {
            $googleUser = Socialite::driver('google')->user();
            $userEmail = $googleUser->getEmail();
            $allowedDomain = '@student.polindra.ac.id';

            // Validasi domain
            if (!str_ends_with($userEmail, $allowedDomain)) {
                return redirect('/login')->with('error', 'Email tidak terdaftar');
            }

            // Cari atau buat user
            $user = User::where('email', $userEmail)->first();
            if ($user) {
                $user->google_id = $googleUser->getId();
            } else {
                $user = User::create([
                    'email' => $userEmail,
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'role' => 'mahasiswa',
                    'password' => null,
                ]);
            }

            // Download avatar (opsional)
            $avatarPath = $user->avatar;
            if (!$avatarPath && $googleAvatarUrl = $googleUser->getAvatar()) {
                $filename = $googleUser->getId() . '.jpg';
                $response = Http::get($googleAvatarUrl);

                if ($response->successful()) {
                    Storage::disk('public')->put('avatar/' . $filename, $response->body());
                    $avatarPath = $filename;
                }
            }

            // Update jika ada avatar baru
            if ($avatarPath && $avatarPath !== $user->avatar) {
                $user->avatar = $avatarPath;
            }

            $user->save();

            DB::commit();
            Auth::login($user, true);
            return redirect()->intended('/dashboard');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Google Login Error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Login gagal. Silakan coba lagi.');
        }
    }
}