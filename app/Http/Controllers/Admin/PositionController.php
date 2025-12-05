<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PositionController extends Controller
{
    public function index()
    {
        $data_jabatan = Jabatan::orderBy('created_at', 'asc')->get();
        return view('content.apps.admin.position.list', compact('data_jabatan'));
    }

    public function create()
    {
        return view('content.apps.admin.position.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatan,nama_jabatan',
        ], [
            'nama_jabatan.unique' => 'Nama jabatan sudah ada, silakan gunakan nama lain.',
            'nama_jabatan.required' => 'Nama jabatan wajib diisi.',
        ]);

        try {
            Jabatan::create($validated);

            return redirect()->route('position.index')
                ->with('success', 'Jabatan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('position.index')
                ->with('error', 'Terjadi kesalahan, data gagal disimpan.');
        }
    }

    // Menampilkan form edit jabatanF
    public function edit($id)
    {
        $data_position = Jabatan::findOrFail($id);
        return view('content.apps.admin.position.edit', compact('data_position'));
    }

    public function update(Request $request, $id)
    {
        $data_jabatan = Jabatan::findOrFail($id);

        $validated = $request->validate([
            'nama_jabatan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('jabatan', 'nama_jabatan')->ignore($data_jabatan->id),
            ],
        ], [
            'nama_jabatan.unique' => 'Nama jabatan sudah digunakan, tidak dapat memperbarui dengan nama yang sama.',
            'nama_jabatan.required' => 'Nama jabatan wajib diisi.',
        ]);

        try {
            $data_jabatan->update($validated);

            return redirect()->route('position.index')
                ->with('success', 'Jabatan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('position.index')
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy($id)
    {
        $data_jabatan = Jabatan::findOrFail($id);

        if (method_exists($data_jabatan, 'staff') && $data_jabatan->staff()->count() > 0) {
            return redirect()->route('position.index')
                ->with('error', 'Data ini masih terkait staf.');
        }

        try {
            $data_jabatan->delete();
            return redirect()->route('position.index')
                ->with('success', 'Jabatan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('position.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}