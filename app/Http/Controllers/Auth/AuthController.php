<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;



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
            // Coba untuk mengautentikasi pengguna
            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                Log::info('User logged in successfully', [
                    'user_id' => Auth::id(),
                    'email' => $credentials['email']
                ]);
                return redirect()->intended(route('dashboard'));
            }
            // Jika credentials salah
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
            return redirect('/login')->with('status', 'Kamu telah berhasil logout.');
        } catch (Exception $e) {
            Log::error('Logout error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id() ?? 'unknown'
            ]);

            // Tetap keluar meskipun error
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->withErrors([
                'message' => 'Terjadi kesalahan saat logout, tetapi Kamu telah dikeluarkan dari sistem.'
            ]);
        }
    }

    public function showForgotForm()
    {
        return view('content.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, $token)
    {
        return view('content.auth.reset-password', [
            'token'   => $token,       
            'email'   => $request->email, 
            'request' => $request, 
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login.')
            : back()->withErrors(['email' => __($status)]);
    }
}
