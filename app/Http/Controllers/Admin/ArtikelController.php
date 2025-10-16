<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\KategoriArtikel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ArtikelController extends Controller
{
    public function index(Request $request)
    {
        $query = Artikel::with(['user', 'kategori'])->orderBy('created_at', 'desc');

        // Filter berdasarkan kategori
        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('kategori_id', $request->kategori);
        }

        // Pencarian berdasarkan judul
        if ($request->has('q') && $request->q != '') {
            $query->where('judul', 'like', '%' . $request->q . '%');
        }

        $artikels = $query->paginate(10)->withQueryString();
        $kategoris = KategoriArtikel::orderBy('kategori')->get();

        return view('admin.kelola-artikel.index', compact('artikels', 'kategoris'));
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
        ]);

        $path = null;
        if ($request->hasFile('gambar')) {
            // Menyimpan gambar ke folder storage/app/public/artikel
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
            'judul' => ['required','string','max:255',Rule::unique('artikel')->ignore($artikel->id)],
            'kategori_id' => 'required|exists:kategori_artikel,id',
            'status' => ['required', Rule::in(['Draft', 'Post'])],
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = $artikel->gambar;
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($path) {
                Storage::delete($path);
            }
            // Simpan gambar baru
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
        // Hapus gambar dari storage jika ada
        if ($artikel->gambar) {
            Storage::delete($artikel->gambar);
        }
        
        $artikel->delete();
        
        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil dihapus.');
    }
}
