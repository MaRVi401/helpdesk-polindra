<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriArtikel;
use Illuminate\Http\Request;


class ArticleCategoryController extends Controller
{
  public function index()
  {
    $data_kategori = KategoriArtikel::orderBy('created_at', 'asc')->get();
    return view('content.apps.admin.article-category.list', compact('data_kategori'));
  }

  public function create()
  {
    return view('content.apps.admin.article-category.create');
  }

  public function store(Request $request)
  {
    $request->validate([
      'kategori' => 'required|string|max:255|unique:kategori_artikel,kategori',
    ], [
      'kategori.unique' => 'Nama kategori ini telah ada.',
    ]);

    KategoriArtikel::create($request->all());

    return redirect()->route('article-category.index')
      ->with('success', 'Kategori baru berhasil ditambahkan.');
  }

  public function edit($id)
  {
    $data_kategori = KategoriArtikel::findOrFail($id);
    return view('content.apps.admin.article-category.edit', compact('data_kategori'));
  }

  public function update(Request $request, $id)
  {
    $request->validate([
      'kategori' => 'required',
      'string',
      'max:255',
    ], [
      'kategori.unique' => 'Nama kategori ini sudah digunakan oleh data lain.',
    ]);

    $data_kategori = KategoriArtikel::findOrFail($id);
    $data_kategori->update($request->all());

    return redirect()->route('article-category.index')
      ->with('success', 'Kategori berhasil diperbarui.');
  }

  public function destroy($id)
  {
    $data_kategori = KategoriArtikel::findOrFail($id);
    if ($data_kategori->artikel()->count() > 0) {
      return redirect()->back()->with('error', 'Kategori artikel ini masih memiliki artikel terkait.');
    }
    try {
      $data_kategori->delete();
      return redirect()->route('article-category.index')->with('success', 'Kategori Artikel berhasil dihapus');
    } catch (\Exception $e) {
      return redirect()->route('article-category.index')->with('error', 'Gagal menghapus Kategori Artikel: ' . $e->getMessage());
    }
  }
}