<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProgramStudiExport;
use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ProgramStudiController extends Controller
{
    public function index(Request $request, Jurusan $jurusan)
    {
        $searchQuery = $request->input('q');
        $perPage = $request->input('per_page', 10);

        $query = $jurusan->programStudis()->withCount('mahasiswa')->orderBy('program_studi', 'asc');

        if ($searchQuery) {
            $query->where('program_studi', 'like', "%{$searchQuery}%");
        }

        $programStudis = $query->paginate($perPage)->withQueryString();

        return view('admin.kelola_jurusan.index_prodi', compact('jurusan', 'programStudis', 'searchQuery', 'perPage'));
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
                'required', 'string', 'max:255',
                Rule::unique('program_studi')->where('jurusan_id', $request->jurusan_id),
            ],
        ], ['program_studi.unique' => 'Nama program studi sudah ada di jurusan ini.']);

        ProgramStudi::create($request->all());
        return redirect()->route('admin.jurusan.program-studi.index', $request->jurusan_id)->with('success', 'Program Studi berhasil ditambahkan.');
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
                'required', 'string', 'max:255',
                Rule::unique('program_studi')->where('jurusan_id', $request->jurusan_id)->ignore($programStudi->id),
            ],
        ], ['program_studi.unique' => 'Nama program studi sudah ada di jurusan ini.']);

        $programStudi->update($request->all());
        return redirect()->route('admin.jurusan.program-studi.index', $request->jurusan_id)->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function destroy(ProgramStudi $programStudi)
    {
        if ($programStudi->mahasiswa()->count() > 0) {
            return redirect()->back()->with('error', 'Gagal menghapus! Program studi ini masih memiliki mahasiswa terdaftar.');
        }
        $jurusanId = $programStudi->jurusan_id;
        try {
            $programStudi->delete();
            return redirect()->route('admin.jurusan.program-studi.index', $jurusanId)->with('success', 'Program Studi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.jurusan.program-studi.index', $jurusanId)->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function exportExcel(Request $request)
    {
        $selectedIds = $request->query('selected_ids', []);

        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk diekspor.');
        }
        
        return Excel::download(new ProgramStudiExport($selectedIds), 'data-program-studi.xlsx');
    }
}

