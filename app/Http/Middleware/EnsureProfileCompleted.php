<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (Auth::check()) {
            $user = Auth::user();
            
            // Cek apakah user adalah mahasiswa dan belum punya profil
            if ($user->role === 'mahasiswa') {
                $mahasiswaExists = Mahasiswa::where('user_id', $user->id)->exists();
                
                // Jika belum punya profil dan bukan sedang di halaman lengkapi-profil
                if (!$mahasiswaExists && !$request->is('lengkapi-profil') && !$request->is('simpan-profil')) {
                    // Set session marker
                    session(['needs_profile_completion' => true]);
                    
                    return redirect('/lengkapi-profil')
                        ->with('warning', 'Silakan lengkapi profil Anda terlebih dahulu untuk melanjutkan.');
                }
            }
        }

        return $next($request);
    }
}