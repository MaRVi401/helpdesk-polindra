<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriArtikel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KategoriArtikelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategoris = KategoriArtikel::orderBy('kategori')->paginate(10);
        return view('admin.kelola-kategori.index', compact('kategoris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.kelola-kategori.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:255|unique:kategori_artikel,kategori',
        ]);

        KategoriArtikel::create($request->all());

        return redirect()->route('admin.kategori-artikel.index')
                         ->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KategoriArtikel $kategoriArtikel)
    {
        return view('admin.kelola-kategori.edit', compact('kategoriArtikel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KategoriArtikel $kategoriArtikel)
    {
        $request->validate([
            'kategori' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kategori_artikel')->ignore($kategoriArtikel->id),
            ],
        ]);

        $kategoriArtikel->update($request->all());

        return redirect()->route('admin.kategori-artikel.index')
                         ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KategoriArtikel $kategoriArtikel)
    {
        // Cek jika kategori masih digunakan oleh artikel
        if ($kategoriArtikel->artikels()->count() > 0) {
            return redirect()->route('admin.kategori-artikel.index')
                             ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh artikel.');
        }

        $kategoriArtikel->delete();

        return redirect()->route('admin.kategori-artikel.index')
                         ->with('success', 'Kategori berhasil dihapus.');
    }
}
