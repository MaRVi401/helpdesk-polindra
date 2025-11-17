<?php

namespace App\Http\Controllers;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    return '';
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
    $request->session()->forget('needs_complete_profile');
    return redirect('/dashboard')->with('success', 'Data profil Kamu telah tersimpan.');
  }
}