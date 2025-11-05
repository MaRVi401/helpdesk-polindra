<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\KategoriArtikel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


class ArticleController extends Controller
{
  public function index(Request $request)
  {
    $data_artikel = Artikel::with(['kategori', 'user'])
      ->orderBy('created_at', 'asc')
      ->get();

    $data_kategori = KategoriArtikel::orderBy('kategori')->get();

    return view('content.apps.admin.article.list', compact('data_artikel', 'data_kategori'));
  }

  public function create()
  {
    $data_kategori = KategoriArtikel::orderBy('kategori')->get();
    return view('content.apps.admin.article.create', compact('data_kategori'));
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
      // Ambil nama asli file (misal: foto1.jpg)
      $originalName = $request->file('gambar')->getClientOriginalName();

      // Simpan dengan nama aslinya ke folder storage/app/public/artikel
      $path = $request->file('gambar')->storeAs('artikel', $originalName, 'public');
    }

    Artikel::create([
      'user_id' => Auth::id(),
      'judul' => $request->judul,
      'kategori_id' => $request->kategori_id,
      'status' => $request->status,
      'deskripsi' => $request->deskripsi,
      'gambar' => $path,
    ]);

    return redirect()->route('article.index')->with('success', 'Artikel berhasil ditambahkan.');
  }



  public function edit(Artikel $artikel)
  {
    $kategoris = KategoriArtikel::orderBy('kategori')->get();
    return view('admin.kelola-artikel.edit', compact('artikel', 'kategoris'));
  }

  public function update(Request $request, Artikel $artikel)
  {
    $request->validate([
      'judul' => ['required', 'string', 'max:255', Rule::unique('artikel')->ignore($artikel->id)],
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

  public function destroy($id)
  {
    try {
      $data_artikel = Artikel::findOrFail($id);
      $data_artikel->delete();

      return redirect()->route('article.index')->with('success', 'Artikel berhasil dihapus');
    } catch (\Exception $e) {
      return redirect()->route('article.index')->with('error', 'Gagal menghapus artikel: ' . $e->getMessage());
    }
  }
}