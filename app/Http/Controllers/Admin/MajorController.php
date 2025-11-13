<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class MajorController extends Controller
{
    public function index(Request $request)
    {
        $data_jurusan = Jurusan::withCount('programStudi')
            ->orderBy('created_at', 'asc')
            ->get();
        return view('content.apps.admin.major.list', compact('data_jurusan'));
    }

    public function create()
    {
        return view('content.apps.admin.major.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jurusan' => 'required|string|max:255|unique:jurusan,nama_jurusan',
        ], [
            'nama_jurusan.unique' => 'Nama jurusan ini sudah ada.'
        ]);

        Jurusan::create($request->all());
        return redirect()->route('major.index')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $data_jurusan = Jurusan::with('programStudi')->findOrFail($id);
        return view('content.apps.admin.major.show', compact('data_jurusan'));
    }

        public function edit($id)
    {
        $data_jurusan = Jurusan::findOrFail($id);
        return view('content.apps.admin.major.edit', compact('data_jurusan'));
    }

    public function update(Request $request, $id)
    {
        $data_jurusan = Jurusan::findOrFail($id);
        $request->validate([
            'nama_jurusan' => [
                'required',
                'string',
                'max:255',
                Rule::unique('jurusan', 'nama_jurusan')->ignore($data_jurusan->id),
            ],
        ], [
            'nama_jurusan.unique' => 'Nama jurusan ini sudah digunakan oleh data lain.'
        ]);

        $data_jurusan->update($request->all());
        return redirect()->route('major.index')->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $data_jurusan = Jurusan::findOrFail($id);
        if ($data_jurusan->programStudi()->count() > 0) {
            return redirect()->route('major.index')->with('error', 'Jurusan ini masih memiliki program studi terkait.');
        }
        try {
            $data_jurusan->delete();
            return redirect()->route('major.index')->with('success', 'Jurusan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('major.index')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}