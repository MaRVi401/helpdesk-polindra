<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitController extends Controller
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
        // Ambil semua staff yang memiliki user untuk ditampilkan di dropdown
        $staffs = Staff::with('user')->whereHas('user')->get();
        return view('admin.kelola-unit.create', compact('staffs'));
    }

    /**
     * Menyimpan unit baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:255|unique:units,nama_unit',
            'kepala_id' => 'nullable|exists:staff,id',
        ], [
            'nama_unit.unique' => 'Nama unit ini sudah ada.',
        ]);

        Unit::create($request->all());

        return redirect()->route('admin.unit.index')->with('success', 'Unit berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit unit.
     */
    public function edit(Unit $unit)
    {
        $staffs = Staff::with('user')->whereHas('user')->get();
        return view('admin.kelola-unit.edit', compact('unit', 'staffs'));
    }

    /**
     * Memperbarui data unit di database.
     */
    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'nama_unit' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units', 'nama_unit')->ignore($unit->id),
            ],
            'kepala_id' => 'nullable|exists:staff,id',
        ], [
            'nama_unit.unique' => 'Nama unit ini sudah digunakan.',
        ]);

        $unit->update($request->all());

        return redirect()->route('admin.unit.index')->with('success', 'Unit berhasil diperbarui.');
    }

    /**
     * Menghapus data unit dari database.
     */
    public function destroy(Unit $unit)
    {
        // Cek apakah ada staff yang masih terhubung dengan unit ini
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
}
