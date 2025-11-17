<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompleteProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Cek apakah user adalah mahasiswa dan belum punya profil
            if ($user->role === 'mahasiswa') {
                $mahasiswaExists = Mahasiswa::where('user_id', $user->id)->exists();
                // Jika belum punya profil dan bukan sedang di halaman /complete-profile
                if (!$mahasiswaExists && !$request->is('complete-profile') && !$request->is('save-profile')) {
                    // Set session marker
                    session(['needs_complete_profile' => true]);
                    return redirect('/complete-profile')
                        ->with('warning', 'Silakan lengkapi profil Anda terlebih dahulu untuk melanjutkan.');
                }
            }
        }
        return $next($request);
    }
}