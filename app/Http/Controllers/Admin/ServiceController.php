<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Unit;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
  public function filterByUnit(Request $request, $slug = null)
  {
    $unitMapping = [
      'upatik' => 'UPA TIK',
      'academy' => 'Akademik',
      'student-affairs' => 'Kemahasiswaan',
      'upt.bahasa' => 'UPT. Bahasa',
    ];

    $query = Layanan::with(['unit', 'penanggungJawab.user'])
      ->orderBy('created_at', 'asc');

    if ($slug && isset($unitMapping[$slug])) {
      $query->whereHas('unit', function ($q) use ($unitMapping, $slug) {
        $q->where('nama_unit', $unitMapping[$slug]);
      });

      // Group hanya 1 unit
      $data_layanan = collect([$unitMapping[$slug] => $query->get()]);
    } else {
      // Tampilkan semua unit
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
          fn($query) =>
          $query->where('unit_id', $request->unit_id)
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
        'unit_id' => $request->unit_id,
        'prioritas' => $request->prioritas,
        'status_arsip' => (int) $request->status_arsip,
      ]);

      // Sinkronkan PIC di tabel
      $data_layanan->penanggungJawab()->sync($request->input('penanggung_jawab_ids', []));
      return $this->redirectToUnitRoute($data_unit->nama_unit, 'Layanan baru berhasil dibuat.');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Gagal membuat layanan. Error: ' . $e->getMessage())
        ->withInput();
    }
  }

  private function getUnitSlug($nama_unit)
  {
    $unitMapping = [
      'UPA TIK' => 'upatik',
      'UPT. Bahasa' => 'upt-bahasa',
      'Akademik' => 'academy',
      'Kemahasiswaan' => 'student-affairs'
    ];

    return $unitMapping[$nama_unit] ?? null;
  }

  public function show($id)
  {
    $data_layanan = Layanan::with([
      'unit',
      'penanggungJawab.user',
    ])->findOrFail($id);
    $unit_slug = $this->getUnitSlug($data_layanan->unit->nama_unit);
    return view('content.apps.admin.service.show', compact('data_layanan', 'unit_slug'));
  }

  public function edit($id)
  {
    $data_layanan = Layanan::with(['unit', 'penanggungJawab'])->findOrFail($id);
    $data_unit = Unit::orderBy('nama_unit')->get();
    $data_staf = Staff::with('user')->get();

    // Tambahkan unit slug untuk back button
    $unit_slug = $this->getUnitSlug($data_layanan->unit->nama_unit);
    return view('content.apps.admin.service.edit', compact('data_layanan', 'data_unit', 'data_staf', 'unit_slug'));
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
      $data_layanan->update([
        'nama' => $request->nama,
        'unit_id' => $request->unit_id,
        'prioritas' => $request->prioritas,
        'status_arsip' => (int) $request->status_arsip,
      ]);

      // Update relasi pivot
      $data_layanan->penanggungJawab()->sync($request->input('penanggung_jawab_ids', []));
      $data_unit = Unit::find($request->unit_id);
      return $this->redirectToUnitRoute($data_unit->nama_unit, 'Layanan berhasil diperbarui.');

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
        return $this->redirectToUnitRoute(
          $data_layanan->unit->nama_unit,
          'Layanan ini masih memiliki tiket terkait.'
        )->with('error', 'Layanan ini masih memiliki tiket terkait.');
      }
      // Simpan nama unit sebelum hapus
      $nama_unit = $data_layanan->unit->nama_unit;

      // Hapus relasi penanggung jawab
      $data_layanan->penanggungJawab()->sync([]);
      // Hapus layanan
      $data_layanan->delete();
      return $this->redirectToUnitRoute($nama_unit, 'Layanan berhasil dihapus.');
    } catch (\Exception $e) {
      return redirect()->back()
        ->with('error', 'Gagal menghapus layanan. Error: ' . $e->getMessage());
    }
  }

  private function redirectToUnitRoute($nama_unit, $successMessage)
  {
    $unitMapping = [
      'UPA TIK' => 'upatik',
      'UPT. Bahasa' => 'upt-bahasa',
      'Akademik' => 'academy',
      'Kemahasiswaan' => 'student-affairs'
    ];

    $slug = $unitMapping[$nama_unit] ?? null;
    if ($slug) {
      return redirect()->route('service.unit.' . $slug)
        ->with('success', $successMessage);
    }
  }

}