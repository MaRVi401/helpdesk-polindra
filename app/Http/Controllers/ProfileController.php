<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\ProgramStudi; // <-- Jangan lupa import model ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Menampilkan form untuk melengkapi profil.
     */
    public function showCompletionForm()
    {
        // Ambil semua data program studi dari database untuk ditampilkan di dropdown
        $programStudi = ProgramStudi::orderBy('program_studi')->get();

        // Arahkan ke view, kirim data program studi bersamanya
        return view('auth.complete-profile', compact('programStudi'));
    }

    /**
     * Menyimpan data profil yang telah dilengkapi.
     */
    public function saveCompletionForm(Request $request)
    {
        // 1. Validasi input dari form
        $request->validate([
            'nim' => 'required|numeric|unique:mahasiswa,nim',
            'program_studi_id' => 'required|exists:program_studi,id',
            'tahun_masuk' => 'required|numeric|digits:4|min:2010',
        ]);

        // 2. Ambil user yang sedang login
        $user = Auth::user();

        // 3. Buat data mahasiswa baru
        Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => $request->nim,
            'program_studi_id' => $request->program_studi_id,
            'tahun_masuk' => $request->tahun_masuk,
        ]);

        // 4. Hapus penanda di session agar tidak diarahkan ke halaman ini lagi
        $request->session()->forget('needs_profile_completion');

        // 5. Arahkan ke dashboard dengan pesan sukses
        return redirect('/dashboard')->with('success', 'Profil Anda berhasil dilengkapi!');
    }
}