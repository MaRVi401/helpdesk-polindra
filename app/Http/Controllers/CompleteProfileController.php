<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CompleteProfileController extends Controller
{
  public function completeProfile()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    $programStudi = ProgramStudi::orderBy('program_studi')->get();

    // Buat daftar tahun secara dinamis
    $years = [];
    $currentYear = date('Y');
    for ($year = $currentYear; $year >= $currentYear - 3; $year--) {
      $years[] = $year;
    }

    // Extract NIM dari email user
    $userEmail = auth()->user()->email;
    $autoNIM = $this->extractNimFromEmail($userEmail);

    return view('content.auth.complete-profile', [
      'pageConfigs' => $pageConfigs,
      'programStudi' => $programStudi,
      'years' => $years,
      'autoNIM' => $autoNIM
    ]);
  }

  /**
   * Extract NIM dari email
   * Logic: 
   * - Jika email mengandung @student.polindra.ac.id DAN bagian sebelum @ adalah numeric -> itu NIM
   * - Jika email mengandung @student.polindra.ac.id tapi bagian sebelum @ bukan numeric -> bukan NIM
   * - Jika email tidak mengandung @student.polindra.ac.id -> bukan NIM student
   */
  private function extractNimFromEmail($email)
  {
    if (str_contains($email, '@student.polindra.ac.id')) {
      $parts = explode('@', $email);

      if (count($parts) > 0) {
        $username = $parts[0];
        // Cek apakah bagian sebelum @ adalah numeric (NIM)
        if (is_numeric($username)) {
          return $username;
        }
      }
    }
    return ''; // Return empty string jika tidak ditemukan NIM valid
  }

  public function saveCompleteProfile(Request $request)
  {
    $request->validate([
      'nim' => 'required|numeric|unique:mahasiswa,nim',
      'program_studi_id' => 'required|exists:program_studi,id',
      'tahun_masuk' => 'required|numeric|digits:4|min:2010|max:' . date('Y'),
    ]);

    $user = Auth::user();
    Mahasiswa::create([
      'user_id' => $user->id,
      'nim' => $request->nim,
      'program_studi_id' => $request->program_studi_id,
      'tahun_masuk' => $request->tahun_masuk,
    ]);

    // Hapus di sesi sehingga tidak akan diarahkan ke halaman ini lagi.
    $request->session()->forget('needs_profile_completion');
    return redirect('/dashboard')->with('success', 'Data profil Kamu telah tersimpan.');
  }


  public function edit()
  {
    $user = Auth::user();

    // Asumsi ada relasi 'mahasiswa' di model User
    $mahasiswa = Mahasiswa::where('id_user', $user->id)->first();

    if (!$mahasiswa) {
      return redirect()
        ->route('mahasiswa.dashboard') // Asumsi nama rute dashboard mahasiswa
        ->with('error', 'Profil mahasiswa tidak ditemukan.');
    }

    // Ambil data yang diperlukan untuk form
    $programStudi = ProgramStudi::orderBy('program_studi')->get();
    $years = [];
    $currentYear = date('Y');
    for ($year = $currentYear; $year >= $currentYear - 5; $year--) {
      $years[] = $year;
    }

    // Tampilkan view
    return view('mahasiswa.profil.edit', compact('user', 'mahasiswa', 'programStudi', 'years'));
  }

  /**
   * Mengupdate data profil mahasiswa.
   */
  public function update(Request $request)
  {
    $user = Auth::user();
    $mahasiswa = $user->mahasiswa; // Asumsi relasi 'mahasiswa' ada di model User

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