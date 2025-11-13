<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudyProgramController extends Controller
{
    public function index(Request $request)
    {
        $data_program_studi = ProgramStudi::with('jurusan')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('content.apps.admin.study-program.list', compact('data_program_studi'));
    }
    public function create()
    {
        $data_jurusan = Jurusan::orderBy('nama_jurusan', 'asc')->get();
        return view('content.apps.admin.study-program.create', compact('data_jurusan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jurusan_id' => 'required|exists:jurusan,id',
            'program_studi' => [
                'required',
                'string',
                'max:255',
                Rule::unique('program_studi')->where('jurusan_id', $request->jurusan_id),
            ],
        ], ['program_studi.unique' => 'Nama program studi sudah ada di jurusan ini.']);

        ProgramStudi::create($request->all());
        return redirect()->route('study-program.index', $request->jurusan_id)->with('success', 'Program Studi berhasil ditambahkan.');
    }

    public function show($id)
    {
        $data_program_studi = ProgramStudi::with('jurusan')->findOrFail($id);
        return view('content.apps.admin.study-program.show', compact('data_program_studi'));
    }

    public function edit($id)
    {
        $data_jurusan = Jurusan::orderBy('nama_jurusan', 'asc')->get();
        $data_program_studi = ProgramStudi::findOrFail($id);
        return view('content.apps.admin.study-program.edit', compact('data_jurusan', 'data_program_studi'));
    }

    public function update(Request $request, $id)
    {

        $data_program_studi = ProgramStudi::findOrFail($id);
        $request->validate([
            'jurusan_id' => 'required|exists:jurusan,id',
            'program_studi' => [
                'required',
                'string',
                'max:255',
                Rule::unique('program_studi')->where('jurusan_id', $request->jurusan_id)->ignore($data_program_studi->id),
            ],
        ], ['program_studi.unique' => 'Nama program studi sudah ada di jurusan ini.']);

        $data_program_studi->update($request->all());
        return redirect()->route('study-program.index', $request->jurusan_id)->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $data_program_studi = ProgramStudi::findOrFail($id);
        if ($data_program_studi->mahasiswa()->count() > 0) {
            return redirect()->back()->with('error', 'Program studi ini masih memiliki mahasiswa terdaftar.');
        }
        try {
            $data_program_studi->delete();
            return redirect()->route('study-program.index', )->with('success', 'Program Studi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('study-program.index', )->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}