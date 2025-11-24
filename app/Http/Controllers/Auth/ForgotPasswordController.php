<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('content.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid'
        ]);

        $status = Password::sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Link reset lupa password telah dikirim ke email Kamu. Silakan cek inbox atau folder spam.')
            : back()->withErrors(['email' => 'Email tidak terdaftar dalam sistem kami.']);
    }

    public function showResetForm(Request $request, $token)
    {
        return view('content.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ], [
            'token.required' => 'Token tidak valid, silakan ulang kembali',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.min' => 'Password minimal 6 karakter'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login dengan password baru Kamu.');
        }

        // Handle different error cases
        $errorMessage = match ($status) {
            Password::INVALID_TOKEN => 'Link reset password tidak valid atau sudah kadaluarsa. Silakan kirim ulang link reset password.',
            Password::INVALID_USER => 'Email tidak ditemukan dalam sistem kami.',
            default => 'Terjadi kesalahan. Silakan coba lagi.'
        };

        return back()->withErrors(['email' => $errorMessage]);
    }
}