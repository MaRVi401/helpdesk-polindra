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
      'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ], [
      'judul.unique' => 'Judul ini sudah digunakan oleh artikel lain.',
    ]);

    $path = null;

    if ($request->hasFile('gambar')) {
      // Request nama asli file
      $originalName = $request->file('gambar')->getClientOriginalName();

      // Simpan dengan nama aslinya ke folder storage/app/public/article
      $path = $request->file('gambar')->storeAs('article', $originalName, 'public');
    }

    // Generate slug dari judul
    $slug = Str::slug($request->judul);
    
    // Pastikan slug unik
    $originalSlug = $slug;
    $counter = 1;
    while (Artikel::where('slug', $slug)->exists()) {
      $slug = $originalSlug . '-' . $counter;
      $counter++;
    }

    Artikel::create([
      'user_id' => Auth::id(),
      'judul' => $request->judul,
      'slug' => $slug,
      'kategori_id' => $request->kategori_id,
      'status' => $request->status,
      'deskripsi' => $request->deskripsi,
      'gambar' => $path,
    ]);

    return redirect()->route('article.index')->with('success', 'Artikel berhasil ditambahkan.');
  }

  public function show($id)
  {
    $data_artikel = Artikel::with(['kategori', 'user'])->findOrFail($id);
    return view('content.apps.admin.article.show', compact('data_artikel'));
  }

  public function edit($id)
  {
    $data_artikel = Artikel::findOrFail($id);
    $data_kategori = KategoriArtikel::all();

    return view('content.apps.admin.article.edit', compact('data_artikel', 'data_kategori'));
  }

  public function update(Request $request, $id)
  {
    $data_artikel = Artikel::findOrFail($id);

    $request->validate([
      'judul' => [
        'required',
        'string',
        'max:255',
        Rule::unique('artikel')->ignore($data_artikel->id),
      ],
      'kategori_id' => 'required|exists:kategori_artikel,id',
      'status' => 'required|in:Draft,Post',
      'deskripsi' => 'required|string',
      'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ], [
      'judul.unique' => 'Judul ini sudah digunakan oleh artikel lain.',
    ]);
    
    // Simpan path lama
    $path = $data_artikel->gambar;

    // Jika upload gambar baru
    if ($request->hasFile('gambar')) {
      // Hapus gambar lama jika ada
      if ($path && Storage::disk('public')->exists($path)) {
        Storage::disk('public')->delete($path);
      }

      // Request nama asli file
      $originalName = $request->file('gambar')->getClientOriginalName();

      // Simpan dengan nama aslinya ke folder storage/app/public/article
      $path = $request->file('gambar')->storeAs('article', $originalName, 'public');
    }

    // Generate slug baru jika judul berubah
    $slug = $data_artikel->slug;
    if ($request->judul !== $data_artikel->judul) {
      $slug = Str::slug($request->judul);
      
      // Pastikan slug unik (kecuali untuk artikel ini sendiri)
      $originalSlug = $slug;
      $counter = 1;
      while (Artikel::where('slug', $slug)->where('id', '!=', $id)->exists()) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
      }
    }

    $data_artikel->update([
      'judul' => $request->judul,
      'slug' => $slug,
      'kategori_id' => $request->kategori_id,
      'status' => $request->status,
      'deskripsi' => $request->deskripsi,
      'gambar' => $path,
    ]);

    return redirect()->route('article.index')->with('success', 'Artikel berhasil diperbarui.');
  }

  public function destroy($id)
  {
    try {
      $data_artikel = Artikel::findOrFail($id);

      // Hapus gambar jika ada
      if ($data_artikel->gambar && Storage::disk('public')->exists($data_artikel->gambar)) {
        Storage::disk('public')->delete($data_artikel->gambar);
      }

      $data_artikel->delete();

      return redirect()->route('article.index')->with('success', 'Artikel berhasil dihapus.');
    } catch (\Exception $e) {
      return redirect()->route('article.index')->with('error', 'Gagal menghapus artikel: ' . $e->getMessage());
    }
  }
}