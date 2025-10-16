<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ArtikelExport;
use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\KategoriArtikel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class ArtikelController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->input('q');
        $perPage = $request->input('per_page', 10);

        $query = Artikel::with(['user', 'kategori'])->orderBy('created_at', 'desc');

        if ($searchQuery) {
            $query->where('judul', 'like', "%{$searchQuery}%");
        }

        $artikels = $query->paginate($perPage)->withQueryString();
        $kategoris = KategoriArtikel::orderBy('kategori')->get();

        return view('admin.kelola-artikel.index', compact('artikels', 'kategoris', 'searchQuery', 'perPage'));
    }

    public function create()
    {
        $kategoris = KategoriArtikel::orderBy('kategori')->get();
        return view('admin.kelola-artikel.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255|unique:artikel,judul',
            'kategori_id' => 'required|exists:kategori_artikel,id',
            'status' => ['required', Rule::in(['Draft', 'Post'])],
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'judul.unique' => 'Judul ini sudah digunakan oleh artikel lain.',
        ]);

        $path = null;
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('public/artikel');
        }

        Artikel::create([
            'user_id' => Auth::id(),
            'judul' => $request->judul,
            'kategori_id' => $request->kategori_id,
            'status' => $request->status,
            'deskripsi' => $request->deskripsi,
            'gambar' => $path,
        ]);

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function edit(Artikel $artikel)
    {
        $kategoris = KategoriArtikel::orderBy('kategori')->get();
        return view('admin.kelola-artikel.edit', compact('artikel', 'kategoris'));
    }

    public function update(Request $request, Artikel $artikel)
    {
        $request->validate([
            'judul' => ['required','string','max:255', Rule::unique('artikel')->ignore($artikel->id)],
            'kategori_id' => 'required|exists:kategori_artikel,id',
            'status' => ['required', Rule::in(['Draft', 'Post'])],
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'judul.unique' => 'Judul ini sudah digunakan oleh artikel lain.',
        ]);

        $path = $artikel->gambar;
        if ($request->hasFile('gambar')) {
            if ($path) {
                Storage::delete($path);
            }
            $path = $request->file('gambar')->store('public/artikel');
        }

        $artikel->update([
            'judul' => $request->judul,
            'kategori_id' => $request->kategori_id,
            'status' => $request->status,
            'deskripsi' => $request->deskripsi,
            'gambar' => $path,
        ]);

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Artikel $artikel)
    {
        if ($artikel->gambar) {
            Storage::delete($artikel->gambar);
        }
        $artikel->delete();
        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $selectedIds = $request->query('selected_ids', []);

        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk diekspor.');
        }

        return Excel::download(new ArtikelExport($selectedIds), 'data-artikel.xlsx');
    }
}