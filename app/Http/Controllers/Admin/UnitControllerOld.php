<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Staff;
use Illuminate\Http\Request;
use App\Exports\UnitExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UnitControllerOld extends Controller
{
    /**
     * Menampilkan halaman daftar semua unit.
     */
    public function index(Request $request)
    {
        $searchQuery = $request->input('q');
        $perPage = $request->input('per_page', 10);

        $query = Unit::with('kepalaUnit.user')->orderBy('nama_unit', 'asc');

        if ($searchQuery) {
            $query->where('nama_unit', 'like', "%{$searchQuery}%");
        }

        $units = $query->paginate($perPage)->withQueryString();

        return view('admin.kelola-unit.index', compact('units', 'searchQuery', 'perPage'));
    }

    /**
     * Menampilkan form untuk membuat unit baru.
     */
    public function create()
    {
        $staffs = Staff::whereHas('user', function ($query) {
            $query->where('role', 'kepala_unit');
        })->with('user')->get();

        return view('admin.kelola-unit.create', compact('staffs'));
    }

    /**
     * Menyimpan unit baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_unit' => 'required|string|max:255|unique:units,nama_unit',
            'kepala_id' => 'nullable|exists:staff,id',
            'slug' => 'nullable|string|max:255|unique:units,slug',
        ], [
            'nama_unit.unique' => 'Nama unit ini sudah ada.',
            'slug.unique' => 'Slug ini sudah digunakan oleh unit lain.',
        ]);

        if (empty($validatedData['slug'])) {
            $validatedData['slug'] = Str::slug($validatedData['nama_unit']);
        }

        Unit::create($validatedData);

        return redirect()->route('admin.unit.index')->with('success', 'Unit berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail unit.
     */
    public function show(Unit $unit)
    {
        $data_unit = $unit->load('kepalaUnit.user');
        return view('content.apps.admin.unit.show', compact('data_unit'));
    }

    /**
     * Menampilkan form untuk mengedit unit.
     */
    public function edit(Unit $unit)
    {
        $staffs = Staff::whereHas('user', function ($query) {
            $query->where('role', 'kepala_unit');
        })->with('user')->get();

        return view('admin.kelola-unit.edit', compact('unit', 'staffs'));
    }

    /**
     * Memperbarui data unit di database.
     */
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'nama_unit' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units', 'nama_unit')->ignore($unit->id),
            ],
            'kepala_id' => 'nullable|exists:staff,id',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('units', 'slug')->ignore($unit->id),
            ],
        ], [
            'nama_unit.unique' => 'Nama unit ini sudah digunakan.',
            'slug.unique' => 'Slug ini sudah digunakan oleh unit lain.',
        ]);

        if (isset($validated['slug']) && empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['nama_unit']);
            $existingSlug = Unit::where('slug', $validated['slug'])->where('id', '!=', $unit->id)->first();
            if ($existingSlug) {
                $validated['slug'] = Str::slug($validated['nama_unit']) . '-' . time();
            }
        }

        $unit->update($validated);

        return redirect()->route('admin.unit.index')->with('success', 'Unit berhasil diperbarui.');
    }

    /**
     * Menghapus data unit dari database.
     */
    public function destroy(Unit $unit)
    {
        if ($unit->staff()->count() > 0) {
            return redirect()->route('admin.unit.index')->with('error', 'Gagal menghapus! Unit ini masih memiliki staff terdaftar.');
        }

        try {
            $unit->delete();
            return redirect()->route('admin.unit.index')->with('success', 'Unit berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.unit.index')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function exportExcel(Request $request)
    {
        $selectedIds = $request->query('selected_ids', []);

        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Tidak ada data unit yang dipilih untuk diekspor.');
        }

        return Excel::download(new UnitExport($selectedIds), 'data-unit.xlsx');
    }
}
