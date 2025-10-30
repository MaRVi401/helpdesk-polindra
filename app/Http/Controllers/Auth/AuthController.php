<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    /**
     * Handle authentication requests.
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            $remember = $request->has('remember');
            // Try to authenticate the user
            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                Log::info('User logged in successfully', [
                    'user_id' => Auth::id(),
                    'email' => $credentials['email']
                ]);
                return redirect()->intended(route('dashboard'));
            }
            // If credentials are wrong
            return back()
                ->withInput($request->only('email', 'remember'))
                ->with('error', 'Email atau Password salah.');

        } catch (ValidationException $e) {
            // Re-throw validation exceptions
            throw $e;
        } catch (Exception $e) {
            Log::error('Login error', [
                'error' => $e->getMessage(),
                'email' => $request->email ?? 'unknown'
            ]);
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Terjadi kesalahan saat login. Silakan coba lagi.');
        }
    }
    public function logout(Request $request)
    {
        try {
            $userId = Auth::id();
            Auth::logout();
            $request->session()->invalidate();

            // Regenerate CSRF token
            $request->session()->regenerateToken();
            Log::info('User logged out successfully', [
                'user_id' => $userId
            ]);
            return redirect('/login')->with('status', 'Anda telah berhasil logout.');

        } catch (Exception $e) {
            Log::error('Logout error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id() ?? 'unknown'
            ]);

            // Still log out even if there is an error
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->withErrors([
                'message' => 'Terjadi kesalahan saat logout, tetapi Anda telah dikeluarkan dari sistem.'
            ]);
        }
    }
}