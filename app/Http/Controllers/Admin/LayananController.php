<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Unit;
use App\Models\Staff;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    /**
     * Menampilkan daftar semua layanan (index).
     */
    public function index(Request $request)
    {
        $searchQuery = $request->input('q');
        $perPage = $request->input('per_page', 10);

        // Gunakan relasi 'penanggungJawab' (dari Layanan.php)
        $query = Layanan::with(['unit', 'penanggungJawab.user'])
                        ->latest();

        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('nama', 'like', "%{$searchQuery}%")
                    ->orWhereHas('unit', function ($subQ) use ($searchQuery) {
                        $subQ->where('nama_unit', 'like', "%{$searchQuery}%");
                    })
                    // Gunakan relasi 'penanggungJawab.user' untuk mencari nama PIC
                    ->orWhereHas('penanggungJawab.user', function ($subQ) use ($searchQuery) {
                        $subQ->where('name', 'like', "%{$searchQuery}%");
                    });
            });
        }

        $layanans = $query->paginate($perPage)->withQueryString();

        return view('admin.layanan.index', compact('layanans', 'searchQuery', 'perPage'));
    }

    /**
     * Menampilkan form untuk membuat layanan baru (create).
     */
    public function create()
    {
        $units = Unit::orderBy('nama_unit')->get();
        // Ambil semua staff dengan data user dan unit mereka
        $allStaff = Staff::with(['user', 'unit'])->get();
        
        return view('admin.layanan.create', compact('units', 'allStaff'));
    }

    /**
     * Menyimpan layanan baru ke database (store).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'prioritas' => 'nullable|integer',
            'penanggung_jawab_ids' => 'nullable|array',
            'penanggung_jawab_ids.*' => 'exists:staff,id', // Validasi setiap ID staff
        ]);

        $layanan = Layanan::create([
            'nama' => $request->nama,
            'unit_id' => $request->unit_id,
            'prioritas' => $request->prioritas ?? 0,
            'status_arsip' => $request->has('status_arsip'),
        ]);

        // Gunakan sync() pada relasi 'penanggungJawab'
        $layanan->penanggungJawab()->sync($request->input('penanggung_jawab_ids', []));

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan baru berhasil dibuat.');
    }

    /**
     * Menampilkan form untuk mengedit layanan (edit).
     */
    public function edit(Layanan $layanan)
    {
        $units = Unit::orderBy('nama_unit')->get();
        $allStaff = Staff::with(['user', 'unit'])->get();
        
        // Ambil ID dari PIC yang saat ini ditugaskan ke layanan ini
        // Menggunakan relasi 'penanggungJawab' dari model Layanan Anda
        $assignedPicIds = $layanan->penanggungJawab->pluck('id');

        return view('admin.layanan.edit', compact('layanan', 'units', 'allStaff', 'assignedPicIds'));
    }

    /**
     * Memperbarui layanan di database (update).
     */
    public function update(Request $request, Layanan $layanan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'prioritas' => 'nullable|integer',
            'penanggung_jawab_ids' => 'nullable|array',
            'penanggung_jawab_ids.*' => 'exists:staff,id',
        ]);

        $layanan->update([
            'nama' => $request->nama,
            'unit_id' => $request->unit_id,
            'prioritas' => $request->prioritas ?? 0,
            'status_arsip' => $request->has('status_arsip'),
        ]);

        // Gunakan sync() untuk memperbarui PIC di tabel pivot
        $layanan->penanggungJawab()->sync($request->input('penanggung_jawab_ids', []));

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    /**
     * Menghapus layanan dari database (destroy).
     */
    public function destroy(Layanan $layanan)
    {
        try {
            // Cek relasi ke tiket
            if ($layanan->tikets()->count() > 0) {
                return redirect()->route('admin.layanan.index')->with('error', 'Gagal menghapus! Layanan ini masih memiliki tiket terkait.');
            }
            
            // Hapus relasi di pivot table dulu
            $layanan->penanggungJawab()->sync([]);
            
            // Hapus layanan
            $layanan->delete();
            
            return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.layanan.index')->with('error', 'Gagal menghapus layanan. Error: ' . $e->getMessage());
        }
    }
}

