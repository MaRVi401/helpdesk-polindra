<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ApiAuthController extends Controller
{
    // Login dengan Email & Password
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Kombinasi email dan password salah.'], 401);
        }

        // Cek Domain (Opsional, sesuaikan kebutuhan)
        // $this->checkDomain($request->email);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'data' => $user,
            'token' => $token
        ]);
    }

    // Login dengan Google (SSO Mobile)
    public function googleLoginMobile(Request $request)
    {
        // Log request masuk untuk debugging
        Log::info('Google Login Request:', $request->all());

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'google_id' => 'required',
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        // 1. Cek Domain Kampus
        $email = $request->email;
        $allowed = ['student.polindra.ac.id', 'gmail.com']; // Tambahkan gmail.com untuk testing jika perlu
        $domain = substr(strrchr($email, "@"), 1);
        
        // if (!in_array($domain, $allowed)) {
        //     return response()->json(['success' => false, 'message' => 'Gunakan email resmi @student.polindra.ac.id'], 403);
        // }

        // 2. Cari atau Buat User
        $user = User::where('email', $email)->first();

        if (!$user) {
            // User baru
            $user = User::create([
                'name' => $request->name,
                'email' => $email,
                'google_id' => $request->google_id,
                'password' => Hash::make('password_default_sso_' . rand(1000,9999)), // Password acak
                'avatar' => $request->avatar,
                'role' => 'mahasiswa' // Default role
            ]);
            
            // Assign role spatie jika ada
            if(method_exists($user, 'assignRole')){
                $user->assignRole('mahasiswa');
            }
        } else {
            // Update Google ID jika user lama login via Google
            if(empty($user->google_id)) {
                $user->update(['google_id' => $request->google_id]);
            }
        }

        // 3. Buat Token
        try {
            $token = $user->createToken('mobile-sso')->plainTextToken;
        } catch (\Exception $e) {
            Log::error("Error Create Token: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal membuat token akses. Cek server logs.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login SSO Berhasil',
            'data' => $user,
            'token' => $token
        ]);
    }
}