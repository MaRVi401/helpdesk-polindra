<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramStudiController extends Controller
{
    public function index()
    {
        $jurusans = Jurusan::orderBy('nama_jurusan', 'asc')->paginate(10, ['*'], 'jurusan_page');
        $programStudis = ProgramStudi::with('jurusan')->orderBy('program_studi', 'asc')->paginate(10, ['*'], 'prodi_page');
        return view('admin.kelola_jurusan.index', compact('jurusans', 'programStudis'));
    }

    public function create()
    {
        $jurusans = Jurusan::orderBy('nama_jurusan', 'asc')->get();
        return view('admin.kelola_jurusan.create_prodi', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jurusan_id' => 'required|exists:jurusan,id',
            'program_studi' => [
                'required',
                'string',
                'max:255',
                Rule::unique('program_studi')->where(function ($query) use ($request) {
                    return $query->where('jurusan_id', $request->jurusan_id);
                }),
            ],
        ], [
            'program_studi.unique' => 'Nama program studi sudah ada di jurusan ini.'
        ]);

        ProgramStudi::create($request->all());
        return redirect()->route('admin.program-studi.index')->with('success', 'Program Studi berhasil ditambahkan.');
    }

    public function edit(ProgramStudi $programStudi)
    {
        $jurusans = Jurusan::orderBy('nama_jurusan', 'asc')->get();
        $prodi = $programStudi;
        return view('admin.kelola_jurusan.edit_prodi', compact('prodi', 'jurusans'));
    }

    public function update(Request $request, ProgramStudi $programStudi)
    {
        $request->validate([
            'jurusan_id' => 'required|exists:jurusan,id',
            'program_studi' => [
                'required',
                'string',
                'max:255',
                Rule::unique('program_studi')->where(function ($query) use ($request) {
                    return $query->where('jurusan_id', $request->jurusan_id);
                })->ignore($programStudi->id),
            ],
        ], [
            'program_studi.unique' => 'Nama program studi sudah ada di jurusan ini.'
        ]);

        $programStudi->update($request->all());
        return redirect()->route('admin.program-studi.index')->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function destroy(ProgramStudi $programStudi)
    {
        if ($programStudi->mahasiswa()->count() > 0) {
            return redirect()->route('admin.program-studi.index')
                ->with('error', 'Gagal menghapus! Program studi ini masih memiliki mahasiswa terdaftar.');
        }

        try {
            $programStudi->delete();
            return redirect()->route('admin.program-studi.index')
                ->with('success', 'Program Studi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.program-studi.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
