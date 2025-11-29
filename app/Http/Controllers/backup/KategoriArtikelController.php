<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriArtikel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriArtikelController extends Controller
{
    public function index()
    {
        $kategoris = KategoriArtikel::orderBy('kategori')->paginate(10);
        return view('admin.kelola-kategori.index', compact('kategoris'));
    }

    public function create()
    {
        return view('admin.kelola-kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:255|unique:kategori_artikel,kategori',
        ], [
            'kategori.unique' => 'Nama kategori ini sudah ada.',
        ]);

        KategoriArtikel::create($request->all());

        return redirect()->route('admin.kategori-artikel.index')
                         ->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function edit(KategoriArtikel $kategoriArtikel)
    {
        return view('admin.kelola-kategori.edit', compact('kategoriArtikel'));
    }

    public function update(Request $request, KategoriArtikel $kategoriArtikel)
    {
        $request->validate([
            'kategori' => ['required','string','max:255', Rule::unique('kategori_artikel')->ignore($kategoriArtikel->id)],
        ], [
            'kategori.unique' => 'Nama kategori ini sudah digunakan oleh data lain.',
        ]);

        $kategoriArtikel->update($request->all());

        return redirect()->route('admin.kategori-artikel.index')
                         ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(KategoriArtikel $kategoriArtikel)
    {
        if ($kategoriArtikel->artikels()->count() > 0) {
            return redirect()->route('admin.kategori-artikel.index')
                             ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh artikel.');
        }

        $kategoriArtikel->delete();

        return redirect()->route('admin.kategori-artikel.index')
                         ->with('success', 'Kategori berhasil dihapus.');
    }
}