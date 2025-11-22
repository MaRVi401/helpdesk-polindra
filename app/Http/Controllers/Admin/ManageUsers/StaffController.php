<?php

namespace App\Http\Controllers\Admin\ManageUsers;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use App\Models\Unit;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
  public function index(Request $request)
  {
    $data_staf = Staff::with(['user', 'unit', 'jabatan'])
      ->orderBy('id', 'asc')
      ->get();

    $data_unit = Unit::orderBy('nama_unit', 'asc')->get();
    $data_jabatan = Jabatan::orderBy('nama_jabatan', 'asc')->get();

    return view('content.apps.admin.manage-users.staff.list', compact('data_staf', 'data_unit', 'data_jabatan'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'nik' => 'required|string|max:20|unique:staff,nik',
      'email' => [
        'required',
        'email',
        'unique:users,email',
      ],
      'role' => [
        'required',
        Rule::in(['admin_unit', 'kepala_unit'])
      ],
      'unit_id' => 'required|exists:units,id',
      'jabatan_id' => 'required|exists:jabatan,id',
    ], [
      'nik.unique' => 'NIK ini sudah terdaftar.',
      'email.unique' => 'Email ini sudah digunakan.',
      'role.in' => 'Role yang dipilih tidak valid.',
    ]);

    // Buat user baru
    $user = User::create([
      'name' => $request->input('name'),
      'email' => $request->input('email'),
      'password' => bcrypt($request->input('nik')), // Password default = NIK
      'role' => $request->input('role')
    ]);

    // Buat data staf
    Staff::create([
      'user_id' => $user->id,
      'nik' => $request->input('nik'),
      'unit_id' => $request->input('unit_id'),
      'jabatan_id' => $request->input('jabatan_id'),
    ]);

    return redirect()->route('staff.index')->with('success', 'Data staf berhasil ditambahkan.');
  }

  public function show($id)
  {
    $data_staf = Staff::with(['user', 'unit', 'jabatan'])->findOrFail($id);

    return view('content.apps.admin.manage-users.staff.show', compact('data_staf'));
  }

  public function edit($id)
  {
    $data_staf = Staff::with(['user', 'unit', 'jabatan'])->findOrFail($id);
    $data_unit = Unit::orderBy('nama_unit', 'asc')->get();
    $data_jabatan = Jabatan::orderBy('nama_jabatan', 'asc')->get();

    return view('content.apps.admin.manage-users.staff.edit', compact('data_staf', 'data_unit', 'data_jabatan'));
  }

  public function update(Request $request, $id)
  {
    $data_staf = Staff::findOrFail($id);
    $isSuperAdmin = $data_staf->user && $data_staf->user->role === 'super_admin';

    $rules = [
      'name' => 'required|string|max:255',
      'nik' => [
        'required',
        'string',
        'max:20',
        Rule::unique('staff', 'nik')->ignore($id),
      ],
      'email' => [
        'required',
        'email',
        Rule::unique('users', 'email')->ignore($data_staf->user_id ?? null),
      ],
      'unit_id' => 'required|exists:units,id',
      'jabatan_id' => 'required|exists:jabatan,id',
    ];


    if (!$isSuperAdmin) {
      $rules['role'] = ['required', Rule::in(['admin_unit', 'kepala_unit'])];
    }

    $validated = $request->validate($rules, [
      'nik.unique' => 'NIK ini sudah terdaftar.',
      'email.unique' => 'Email ini sudah digunakan.',
      'role.in' => 'Role yang dipilih tidak valid.',
    ]);

    if ($isSuperAdmin) {
      $validated['role'] = 'super_admin';
    }

    if ($data_staf->user) {
      $data_staf->user->update([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'role' => $validated['role'],
      ]);
    }

    $data_staf->update([
      'nik' => $validated['nik'],
      'unit_id' => $validated['unit_id'],
      'jabatan_id' => $validated['jabatan_id'],
    ]);

    return redirect()->route('staff.index')->with('success', 'Data staff berhasil diperbarui.');
  }

  public function destroy($id)
  {
    try {
      $data_staf = Staff::findOrFail($id);

      // Cek apakah user terkait memiliki role super_admin
      if ($data_staf->user && $data_staf->user->role === 'super_admin') {
        return redirect()->route('staff.index')
          ->with('error', 'Akun dengan role Super Admin tidak dapat dihapus.');
      }

      // Hapus user terkait jika bukan super_admin
      if ($data_staf->user) {
        $data_staf->user->delete();
      }

      $data_staf->delete();

      return redirect()->route('staff.index')
        ->with('success', 'Data staff dan akun pengguna berhasil dihapus.');
    } catch (\Exception $e) {
      return redirect()->route('staff.index')
        ->with('error', 'Gagal menghapus data staff: ' . $e->getMessage());
    }
  }

}