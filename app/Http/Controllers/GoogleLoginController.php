<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mahasiswa;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Hash; // Tambahkan ini
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
            DB::beginTransaction(); // Mulai transaksi untuk memastikan konsistensi data
            
            $googleUser = Socialite::driver('google')->user();
            $userEmail = $googleUser->getEmail();
            $allowedDomain = '@student.polindra.ac.id';
            
            // --- LOGIC PASSWORD OTOMATIS ---
            // 1. Ambil bagian lokal email (sebelum @)
            $emailParts = explode('@', $userEmail);
            $localPart = $emailParts[0]; 
            
            // 2. Ambil tahun saat ini
            $currentYear = date('Y');
            
            // 3. Gabungkan dan hash password
            $defaultPassword = $localPart . $currentYear;
            $hashedPassword = Hash::make($defaultPassword);

            // --- VALIDASI DOMAIN EMAIL ---
            if (!str_ends_with($userEmail, $allowedDomain)) {
                return redirect('/login')->with('error', 'Email anda tidak terdata dalam data kampus');
            }

            // --- DOWNLOAD AVATAR LOKAL ---
            $googleAvatarUrl = $googleUser->getAvatar();
            $avatarPath = null;
            $filename = $googleUser->getId() . '.jpg';
            // Path tempat file akan disimpan di storage/app/public/avatar/
            $storageDir = 'avatar'; 
            
            if ($googleAvatarUrl) {
                // Gunakan Http facade yang lebih aman
                $response = Http::get($googleAvatarUrl);

                if ($response->successful()) {
                    // Simpan file ke folder public/avatar
                    // Penyimpanan menggunakan Storage::put(direktori/namafile)
                    Storage::disk('public')->put($storageDir . '/' . $filename, $response->body());
                    
                    // Hanya simpan NAMA FILE ke database
                    $avatarPath = $filename; 
                }
            }

            // Cari user berdasarkan email
            $user = User::where('email', $userEmail)->first();

            if ($user) {
                $user->google_id = $googleUser->getId();
                if ($avatarPath) {
                    $user->avatar = $avatarPath;
                }
                $user->save();
            } else {
                $user = User::create([
                    'email' => $userEmail,
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $avatarPath,
                    'role' => 'mahasiswa',
                    'password' => $hashedPassword, 
                ]);
            }

            DB::commit(); 

            Auth::login($user, true);

            return redirect()->intended('/dashboard');
            
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Google Login Error: ' . $e->getMessage()); 
            return redirect('/login')->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }
}