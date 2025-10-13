<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.program-studi.index');
    }

    public function create()
    {
        return view('admin.kelola_jurusan.create_jurusan');
    }

    public function store(Request $request)
    {
        $request->validate(['nama_jurusan' => 'required|string|max:255|unique:jurusan,nama_jurusan']);
        Jurusan::create($request->all());
        return redirect()->route('admin.program-studi.index')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit(Jurusan $jurusan)
    {
        return view('admin.kelola_jurusan.edit_jurusan', compact('jurusan'));
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate(['nama_jurusan' => 'required|string|max:255|unique:jurusan,nama_jurusan,' . $jurusan->id]);
        $jurusan->update($request->all());
        return redirect()->route('admin.program-studi.index')->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Jurusan $jurusan)
    {
        if ($jurusan->programStudis()->count() > 0) {
            return redirect()->route('admin.program-studi.index')
                ->with('error', 'Gagal menghapus! Jurusan ini masih memiliki program studi terkait.');
        }
        $jurusan->delete();

        return redirect()->route('admin.program-studi.index')
            ->with('success', 'Jurusan berhasil dihapus.');
    }
}
