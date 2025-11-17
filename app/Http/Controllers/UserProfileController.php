<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    public function userProfile()
    {
        // $user = Auth::user();
        

        return view('content.user-profile.profile');
    }

    public function updateUserProfile(Request $request)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            return redirect()->back()->with('error', 'Profil mahasiswa tidak ditemukan.');
        }

        // Validasi data
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nim' => [
                'required',
                'string',
                'max:20',
                Rule::unique('mahasiswa', 'nim')->ignore($mahasiswa->id_mahasiswa, 'id_mahasiswa'), // Sesuaikan 'id_mahasiswa' jika nama PK beda
            ],
            'id_program_studi' => 'required|exists:program_studi,id',
            'tahun_masuk' => 'required|numeric|digits:4|min:2010',
            'no_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 1. Update data di tabel 'users'
        $user->update([
            'nama' => $request->nama,
            'email' => $request->email,
        ]);

        // 2. Siapkan data untuk tabel 'mahasiswa'
        $mahasiswaData = [
            'nim' => $request->nim,
            'id_program_studi' => $request->id_program_studi,
            'tahun_masuk' => $request->tahun_masuk,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ];

        // 3. Handle upload foto
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada
            if ($mahasiswa->foto_profil) {
                Storage::delete('public/' . $mahasiswa->foto_profil);
            }

            // Simpan foto baru ke 'storage/app/public/foto_profil'
            $path = $request->file('foto_profil')->store('public/foto_profil');

            // Simpan path relatif di database
            $mahasiswaData['foto_profil'] = str_replace('public/', '', $path);
        }

        // 4. Update data di tabel 'mahasiswa'
        $mahasiswa->update($mahasiswaData);

        return redirect()->route('mahasiswa.profil.edit')->with('success', 'Profil berhasil diperbarui!');
    }
}