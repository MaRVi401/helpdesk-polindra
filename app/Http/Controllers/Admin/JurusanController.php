<?php

namespace App\Http\Controllers\Admin;

use App\Exports\JurusanExport;
use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class JurusanController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->input('q');
        $perPage = $request->input('per_page', 10);

        $query = Jurusan::withCount('programStudis')->orderBy('nama_jurusan', 'asc');

        if ($searchQuery) {
            $query->where('nama_jurusan', 'like', "%{$searchQuery}%");
        }

        $jurusans = $query->paginate($perPage)->withQueryString();

        return view('admin.kelola_jurusan.index_jurusan', compact('jurusans', 'searchQuery', 'perPage'));
    }

    public function create()
    {
        return view('admin.kelola_jurusan.create_jurusan');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jurusan' => 'required|string|max:255|unique:jurusan,nama_jurusan',
        ], [
            'nama_jurusan.unique' => 'Nama jurusan ini sudah ada.'
        ]);
        
        Jurusan::create($request->all());
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit(Jurusan $jurusan)
    {
        return view('admin.kelola_jurusan.edit_jurusan', compact('jurusan'));
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'nama_jurusan' => [
                'required', 'string', 'max:255',
                Rule::unique('jurusan', 'nama_jurusan')->ignore($jurusan->id),
            ],
        ], [
            'nama_jurusan.unique' => 'Nama jurusan ini sudah digunakan oleh data lain.'
        ]);

        $jurusan->update($request->all());
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Jurusan $jurusan)
    {
        if ($jurusan->programStudis()->count() > 0) {
            return redirect()->route('admin.jurusan.index')->with('error', 'Gagal menghapus! Jurusan ini masih memiliki program studi terkait.');
        }

        try {
            $jurusan->delete();
            return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.jurusan.index')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function exportExcel(Request $request)
    {
        $selectedIds = $request->query('selected_ids', []);

        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk diekspor.');
        }

        return Excel::download(new JurusanExport($selectedIds), 'data-jurusan.xlsx');
    }
}

