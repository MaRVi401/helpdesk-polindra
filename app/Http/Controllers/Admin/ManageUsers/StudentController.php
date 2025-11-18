<?php

namespace App\Http\Controllers\Admin\ManageUsers;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

  public function show($id)
  {
    $data_mahasiswa = Mahasiswa::with(['user', 'programStudi.jurusan'])->findOrFail($id);
    return view('content.apps.admin.manage-users.student.show', compact('data_mahasiswa'));
  }

  public function edit($id)
  {
    $data_mahasiswa = Mahasiswa::findOrFail($id);
    $data_program_studi = ProgramStudi::with('jurusan')
      ->orderBy('program_studi', 'asc')
      ->get()
      ->groupBy(fn($item) => $item->jurusan->nama_jurusan)
      ->sortBy(fn($prodi, $nama_jurusan) => $prodi->first()->jurusan->id);
    return view('content.apps.admin.manage-users.student.edit', compact('data_mahasiswa', 'data_program_studi'));
  }

  public function update(Request $request, $id)
  {
    $data_mahasiswa = Mahasiswa::findOrFail($id);

    $request->validate([
      'name' => 'required|string|max:255',
      'nim' => [
        'required',
        'string',
        'max:20',
        Rule::unique('mahasiswa', 'nim')->ignore($id),
      ],
      'email' => [
        'required',
        'email',
        'regex:/^[a-zA-Z0-9._%+-]+@student\.polindra\.ac\.id$/',
        Rule::unique('users', 'email')->ignore($data_mahasiswa->user_id ?? null),
      ],
      'program_studi' => 'required|exists:program_studi,id',
      'tahun_masuk' => 'required|integer|min:2000|max:2100',
    ], [
      'nim.unique' => 'NIM ini sudah terdaftar.',
      'email.regex' => 'Email harus menggunakan domain @student.polindra.ac.id.',
      'email.unique' => 'Email ini sudah digunakan.',
    ]);

    // Update data user (nama dan email)
    if ($data_mahasiswa->user) {
      $data_mahasiswa->user->update([
        'name' => $request->name,
        'email' => $request->email,
      ]);
    }
    $data_mahasiswa->update([
      'nim' => $request->nim,
      'program_studi_id' => $request->program_studi,
      'tahun_masuk' => $request->tahun_masuk,
    ]);

    return redirect()->route('student.index')->with('success', 'Data mahasiswa berhasil diperbarui.');
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

      return redirect()->route('student.index')->with('success', 'Data mahasiswa dan akun pengguna berhasil dihapus.');
    } catch (\Exception $e) {
      return redirect()->route('student.index')->with('error', 'Gagal menghapus data mahasiswa: ' . $e->getMessage());
    }
  }
}