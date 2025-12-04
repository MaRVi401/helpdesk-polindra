<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PositionControllerOld extends Controller
{
    public function index()
    {
        $data_positions = Jabatan::orderBy('created_at', 'asc')->get();
        return view('admin.position.index', compact('data_positions'));
    }

    public function create()
    {
        return view('admin.position.create');
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
                ->with('success', 'Position successfully added.');
        } catch (\Exception $e) {
            return redirect()->route('position.index')
                ->with('error', 'Terjadi kesalahan, data gagal disimpan.');
        }
    }


    public function show($id)
    {
        return redirect()->route('position.index');
    }

    public function edit($id)
    {
        $data_position = Jabatan::findOrFail($id);
        return view('admin.position.edit', compact('data_position'));
    }

    public function update(Request $request, $id)
    {
        $data_position = Jabatan::findOrFail($id);

        $validated = $request->validate([
            'nama_jabatan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('jabatan', 'nama_jabatan')->ignore($data_position->id),
            ],
        ], [
            'nama_jabatan.unique' => 'Nama jabatan sudah digunakan, tidak dapat memperbarui dengan nama yang sama.',
            'nama_jabatan.required' => 'Nama jabatan wajib diisi.',
        ]);

        try {
            $data_position->update($validated);

            return redirect()->route('position.index')
                ->with('success', 'Position successfully updated.');
        } catch (\Exception $e) {
            return redirect()->route('position.index')
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }


    public function destroy($id)
    {
        $data_position = Jabatan::findOrFail($id);

        if (method_exists($data_position, 'staff') && $data_position->staff()->count() > 0) {
            return redirect()->route('position.index')
                ->with('error', 'Jangan Dihapus! Data ini masih memiliki relasi dengan staff.');
        }

        try {
            $data_position->delete();
            return redirect()->route('position.index')
                ->with('success', 'Position successfully deleted.');
        } catch (\Exception $e) {
            return redirect()->route('position.index')
                ->with('error', 'An error occurred while deleting the data.');
        }
    }
}
