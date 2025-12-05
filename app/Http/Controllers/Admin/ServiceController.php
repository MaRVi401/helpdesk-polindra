<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Unit;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
  public function filterByUnit($slug = null)
  {
    $query = Layanan::with(['unit', 'penanggungJawab.user'])
      ->orderBy('created_at', 'asc');

    if ($slug) {
      $unit = Unit::where('slug', $slug)->firstOrFail();
      $query->where('unit_id', $unit->id);
      $data_layanan = collect([$unit->nama_unit => $query->get()]);
    } else {
      $data_layanan = $query->get()->groupBy(function ($item) {
        return $item->unit->nama_unit ?? 'Lainnya';
      });
    }

    $data_unit = Unit::orderBy('nama_unit')->get();
    $data_staf = Staff::with('user')->get();

    return view('content.apps.admin.service.list', compact('data_layanan', 'slug', 'data_unit', 'data_staf'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'nama' => [
        'required',
        'string',
        'max:255',
        Rule::unique('layanan', 'nama')->where(
          fn($query) => $query->where('unit_id', $request->unit_id)
        ),
      ],
      'unit_id' => 'required|exists:units,id',
      'prioritas' => 'required|integer|in:1,2,3',
      'penanggung_jawab_ids' => 'nullable|array',
      'penanggung_jawab_ids.*' => 'exists:staff,id',
      'status_arsip' => 'required|in:0,1',
    ], [
      'nama.unique' => 'Nama layanan sudah digunakan pada unit ini.',
    ]);

    try {
      // Ambil unit untuk redirect
      $data_unit = Unit::findOrFail($request->unit_id);

      $data_layanan = Layanan::create([
        'nama' => $request->nama,
        'slug' => Str::slug($request->nama),
        'unit_id' => $request->unit_id,
        'prioritas' => $request->prioritas,
        'status_arsip' => (int) $request->status_arsip,
      ]);

      // Sinkronkan PIC di tabel
      $data_layanan->penanggungJawab()->sync($request->input('penanggung_jawab_ids', []));

      return $this->redirectToUnitRoute($data_unit->slug, 'Layanan baru berhasil dibuat.');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Gagal membuat layanan. Error: ' . $e->getMessage())
        ->withInput();
    }
  }

  public function show($unitSlug, $layananSlug)
  {
    $unit = Unit::where('slug', $unitSlug)->firstOrFail();

    $data_layanan = Layanan::where('slug', $layananSlug)
      ->where('unit_id', $unit->id)
      ->with(['unit', 'penanggungJawab.user'])
      ->firstOrFail();

    return view('content.apps.admin.service.show', compact('data_layanan', 'unitSlug'));
  }

  public function edit($unitSlug, $layananSlug)
  {
    $unit = Unit::where('slug', $unitSlug)->firstOrFail();

    $data_layanan = Layanan::where('slug', $layananSlug)
      ->where('unit_id', $unit->id)
      ->with(['unit', 'penanggungJawab'])
      ->firstOrFail();

    $data_unit = Unit::orderBy('nama_unit')->get();
    $data_staf = Staff::with('user')->get();

    return view('content.apps.admin.service.edit', compact(
      'data_layanan',
      'data_unit',
      'data_staf',
      'unitSlug'
    ));
  }

  public function update(Request $request, $id)
  {
    $data_layanan = Layanan::findOrFail($id);

    $request->validate([
      'nama' => [
        'required',
        'string',
        'max:255',
        Rule::unique('layanan', 'nama')
          ->where(fn($query) => $query->where('unit_id', $request->unit_id))
          ->ignore($data_layanan->id)
      ],
      'unit_id' => 'required|exists:units,id',
      'prioritas' => 'required|integer|in:1,2,3',
      'penanggung_jawab_ids' => 'nullable|array',
      'penanggung_jawab_ids.*' => 'exists:staff,id',
      'status_arsip' => 'required|in:0,1',
    ], [
      'nama.unique' => 'Nama layanan sudah digunakan pada unit ini.',
    ]);

    try {
      // Ambil unit untuk redirect
      $data_unit = Unit::findOrFail($request->unit_id);

      $data_layanan->update([
        'nama' => $request->nama,
        'slug' => Str::slug($request->nama),
        'unit_id' => $request->unit_id,
        'prioritas' => $request->prioritas,
        'status_arsip' => (int) $request->status_arsip,
      ]);

      // Update relasi pivot
      $data_layanan->penanggungJawab()->sync($request->input('penanggung_jawab_ids', []));

      return $this->redirectToUnitRoute($data_unit->slug, 'Layanan berhasil diperbarui.');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Gagal memperbarui layanan. Error: ' . $e->getMessage())
        ->withInput();
    }
  }

  public function destroy($id)
  {
    try {
      $data_layanan = Layanan::findOrFail($id);

      if ($data_layanan->tiket()->count() > 0) {
        return redirect()->back()
          ->with('error', 'Layanan ini masih memiliki tiket terkait.');
      }

      // Simpan slug unit sebelum hapus
      $unit_slug = $data_layanan->unit->slug;

      // Hapus relasi penanggung jawab
      $data_layanan->penanggungJawab()->sync([]);

      // Hapus layanan
      $data_layanan->delete();

      return $this->redirectToUnitRoute($unit_slug, 'Layanan berhasil dihapus.');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Gagal menghapus layanan. Error: ' . $e->getMessage());
    }
  }

  /**
   * Redirect to unit service list page
   *
   * @param string|null $unit_slug
   * @param string $successMessage
   * @return \Illuminate\Http\RedirectResponse
   */
  private function redirectToUnitRoute($unit_slug, $successMessage)
  {
    if ($unit_slug) {
      return redirect()->route('service.unit', ['unitSlug' => $unit_slug])
        ->with('success', $successMessage);
    }

    // Fallback jika slug tidak ada
    return redirect()->back()
      ->with('success', $successMessage);
  }
}