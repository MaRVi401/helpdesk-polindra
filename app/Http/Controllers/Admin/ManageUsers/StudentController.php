<?php

namespace App\Http\Controllers\Admin\ManageUsers;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class StudentController extends Controller
{
  public function index(Request $request)
  {
    $data_mahasiswa = Mahasiswa::with(['user', 'programStudi'])
      ->orderBy('id', 'asc')
      ->get();
    $data_program_studi = ProgramStudi::with('jurusan')
      ->orderBy('program_studi', 'asc')
      ->get()
      ->groupBy(fn($item) => $item->jurusan->nama_jurusan)
      ->sortBy(fn($prodi, $nama_jurusan) => $prodi->first()->jurusan->id);

    return view('content.apps.admin.manage-users.student.list', compact('data_mahasiswa', 'data_program_studi'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'nim' => 'required|string|max:20|unique:mahasiswa,nim',
      'email' => [
        'required',
        'email',
        'regex:/^[a-zA-Z0-9._%+-]+@student\.polindra\.ac\.id$/',
        'unique:users,email',
      ],
      'program_studi' => 'required|exists:program_studi,id',
      'tahun_masuk' => 'required|integer|min:2000|max:2100',
    ], [
      'nim.unique' => 'NIM ini sudah terdaftar.',
      'email.regex' => 'Email harus menggunakan domain @student.polindra.ac.id.',
      'email.unique' => 'Email ini sudah digunakan.',
    ]);

    // Buat user baru (mahasiswa)
    $user = User::create([
      'name' => $request->input('name'),
      'email' => $request->input('email'),
      'password' => bcrypt($request->input('nim')),
      'role' => 'mahasiswa'
    ]);

    Mahasiswa::create([
      'user_id' => $user->id,
      'nim' => $request->input('nim'),
      'program_studi_id' => $request->input('program_studi'),
      'tahun_masuk' => $request->input('tahun_masuk')
    ]);


    return redirect()->route('student.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
  }

  public function destroy($id)
  {
    try {
      $data_mahasiswa = Mahasiswa::findOrFail($id);

      // Hapus user terkait
      if ($data_mahasiswa->user) {
        $data_mahasiswa->user->delete();
      }
      $data_mahasiswa->delete();

      return redirect()->route('student.index')->with('success', 'Data mahasiswa dan akun user berhasil dihapus.');
    } catch (\Exception $e) {
      return redirect()->route('student.index')->with('error', 'Gagal menghapus data mahasiswa: ' . $e->getMessage());
    }
  }


  // public function edit(Mahasiswa $mahasiswa)
  // {
  //   $mahasiswa->load(['user', 'programStudi']);

  //   $users = User::where('role', 'mahasiswa')->get();
  //   $programStudis = ProgramStudi::all();

  //   // Buat daftar tahun secara dinamis (contoh: 15 tahun ke belakang)
  //   $years = [];
  //   $currentYear = date('Y');
  //   for ($year = $currentYear; $year >= $currentYear - 5; $year--) {
  //     $years[] = $year;
  //   }

  //   return view('admin.kelolapengguna.mahasiswa.edit', compact('mahasiswa', 'users', 'programStudis', 'years'));
  // }

  // public function update(Request $request, Mahasiswa $mahasiswa)
  // {
  //   // 1. Validasi data yang masuk
  //   $request->validate([
  //     // Aturan validasi untuk data user
  //     'name' => 'required|string|max:255',
  //     'email' => 'required|email|unique:users,email,' . $mahasiswa->user_id,

  //     // Aturan validasi untuk data mahasiswa
  //     'program_studi_id' => 'required|exists:program_studi,id',
  //     'nim' => 'required|string|unique:mahasiswa,nim,' . $mahasiswa->id,
  //     'tahun_masuk' => 'required|digits:4|integer|min:1900',
  //   ]);

  //   try {
  //     // 2. Gunakan DB Transaction untuk keamanan
  //     DB::transaction(function () use ($request, $mahasiswa) {
  //       // Update data di tabel users
  //       $mahasiswa->user()->update([
  //         'name' => $request->name,
  //         'email' => $request->email,
  //       ]);

  //       // Update data di tabel mahasiswa
  //       $mahasiswa->update([
  //         'program_studi_id' => $request->program_studi_id,
  //         'nim' => $request->nim,
  //         'tahun_masuk' => $request->tahun_masuk,
  //       ]);
  //     });
  //   } catch (\Exception $e) {
  //     return back()->withErrors(['error' => 'Gagal memperbarui data: ' . $e->getMessage()]);
  //   }

  //   // 3. Redirect kembali dengan pesan sukses
  //   return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui.');
  // }



}