<?php

namespace App\Http\Controllers\Auth;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        // Tambahkan parameter untuk selalu menampilkan pemilihan akun
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    /**
     * Handle the Google callback, verify the email domain, and log in the user.
     */
    public function handleGoogleCallback()
    {
        try {
            DB::beginTransaction();

            $googleUser = Socialite::driver('google')->user();
            $userEmail = $googleUser->getEmail();
            $allowedDomain = '@student.polindra.ac.id';

            // Validasi domain email
            if (!str_ends_with($userEmail, $allowedDomain)) {
                return redirect('/login')->with('error', 'Email anda tidak terdata dalam data kampus');
            }

            // Cari user berdasarkan email
            $user = User::where('email', $userEmail)->first();

            if ($user) {
                $user->google_id = $googleUser->getId();
            } else {
                // --- LOGIC PASSWORD OTOMATIS ---
                $emailParts = explode('@', $userEmail);
                $localPart = $emailParts[0];
                $currentYear = date('Y');
                $defaultPassword = $localPart . $currentYear;
                $hashedPassword = Hash::make($defaultPassword);

                $user = User::create([
                    'email' => $userEmail,
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'role' => 'mahasiswa',
                    'password' => null,
                ]);
            }

            // --- CEK DAN DOWNLOAD AVATAR HANYA JIKA BELUM ADA ---
            $avatarPath = $user->avatar ?? null; // ambil avatar lama jika ada
            if (!$avatarPath && $googleAvatarUrl = $googleUser->getAvatar()) {
                $filename = $googleUser->getId() . '.jpg';
                $storageDir = 'avatar';

                $response = Http::get($googleAvatarUrl);
                if ($response->successful()) {
                    Storage::disk('public')->put($storageDir . '/' . $filename, $response->body());
                    $avatarPath = $filename;
                }
            }

            // Simpan avatar jika ada perubahan
            if ($avatarPath) {
                $user->avatar = $avatarPath;
            }

            $user->save();

            DB::commit();
            Auth::login($user, true);

            return redirect()->intended('/dashboard');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Google Login Error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }
}
