<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Unit;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LayananController extends Controller
{
    /**
     * Menampilkan daftar semua layanan (index).
     */
    public function index(Request $request)
    {
        $searchQuery = $request->input('q');
        // Tambahkan pengambilan unit_id dari request
        $filterUnitId = $request->input('unit_id');
        $perPage = $request->input('per_page', 10);

        // Ambil semua unit untuk ditampilkan sebagai filter
        $units = Unit::orderBy('nama_unit')->get(); //

        // Gunakan relasi 'penanggungJawab' (dari Layanan.php)
        $query = Layanan::with(['unit', 'penanggungJawab.user']) //
            ->latest(); //

        // --- Tambahkan logika filter berdasarkan unit_id ---
        if ($filterUnitId) {
            $query->where('unit_id', $filterUnitId);
        }
        // ----------------------------------------------------

        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('nama', 'like', "%{$searchQuery}%") //
                    ->orWhereHas('unit', function ($subQ) use ($searchQuery) {
                        $subQ->where('nama_unit', 'like', "%{$searchQuery}%"); //
                    })
                    // Gunakan relasi 'penanggungJawab.user' untuk mencari nama PIC
                    ->orWhereHas('penanggungJawab.user', function ($subQ) use ($searchQuery) {
                        $subQ->where('name', 'like', "%{$searchQuery}%"); //
                    });
            });
        }

        $layanans = $query->paginate($perPage)->withQueryString(); //

        // Tambahkan 'units' dan 'filterUnitId' ke compact untuk dioper ke view
        return view('admin.layanan.index', compact('layanans', 'searchQuery', 'perPage', 'units', 'filterUnitId')); //
    }

    /**
     * Menampilkan form untuk membuat layanan baru (create).
     */
    public function create()
    {
        $units = Unit::orderBy('nama_unit')->get();
        // Mengambil semua Staff untuk form create
        // Staff harus di-load dengan relasi user dan nik untuk tampilan PIC interaktif
        $allStaff = Staff::with('user')->get();

        return view('admin.layanan.create', compact('units', 'allStaff'));
    }

    /**
     * Menyimpan layanan baru ke database (store).
     */
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
            'prioritas' => 'nullable|integer',
            'penanggung_jawab_ids' => 'nullable|array',
            'penanggung_jawab_ids.*' => 'exists:staff,id',
            'status_arsip' => 'required|in:0,1',
        ], [
            'nama.unique' => 'Nama layanan sudah digunakan pada unit ini.',
        ]);

        try {
            $layanan = Layanan::create([
                'nama' => $request->nama,
                'unit_id' => $request->unit_id,
                'prioritas' => $request->prioritas ?? 0,
                // Gunakan nilai integer langsung dari select
                'status_arsip' => (int) $request->status_arsip,
            ]);

            // Sinkronkan PIC di tabel pivot
            $layanan->penanggungJawab()->sync($request->input('penanggung_jawab_ids', []));

            return redirect()->route('admin.layanan.index')->with('success', 'Layanan baru berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat layanan. Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan form untuk mengedit layanan tertentu, termasuk PIC.
     */
    public function edit(Layanan $layanan)
    {
        $units = Unit::orderBy('nama_unit')->get();
        // Ambil SEMUA staff dengan relasi user untuk display
        $allStaff = Staff::with('user')->get();

        // Ambil staff yang sudah ditugaskan sebagai PIC untuk layanan ini
        $currentPICS = $layanan->penanggungJawab()->with('user')->get();

        // Pisahkan staff yang sudah menjadi PIC dari calon PIC yang tersedia
        $assignedPICIds = $currentPICS->pluck('id')->toArray();
        $availableStaff = $allStaff->reject(function ($staff) use ($assignedPICIds) {
            return in_array($staff->id, $assignedPICIds);
        });

        return view('admin.layanan.edit', compact('layanan', 'units', 'currentPICS', 'availableStaff'));
    }

    /**
     * Memperbarui layanan di database, termasuk mengelola PIC (Data Dasar, Tambah PIC, Hapus PIC).
     */
    public function update(Request $request, Layanan $layanan)
    {
        try {
            // Kita bungkus semua dalam transaksi
            return DB::transaction(function () use ($request, $layanan) {

                // --- 1. Logika Hapus PIC (Dipicu dari tombol Hapus) ---
                if ($request->filled('pic_id_to_remove')) {
                    $request->validate(['pic_id_to_remove' => 'required|exists:staff,id']);

                    $layanan->penanggungJawab()->detach($request->pic_id_to_remove);
                    return redirect()->route('admin.layanan.edit', $layanan)
                        ->with('success', 'Penanggung Jawab berhasil dihapus.');
                }

                // --- 2. Logika Tambah PIC (Dipicu dari form Tambah PIC) ---
                if ($request->filled('pic_id_to_add')) {
                    $request->validate(['pic_id_to_add' => 'required|exists:staff,id']);

                    // Attach PIC baru ke relasi many-to-many
                    $layanan->penanggungJawab()->attach($request->pic_id_to_add);
                    return redirect()->route('admin.layanan.edit', $layanan)
                        ->with('success', 'Penanggung Jawab berhasil ditambahkan.');
                }

                // --- 3. Logika Update Data Dasar Layanan (Dipicu dari form Update Data Dasar) ---

                // Jalankan validasi penuh untuk data dasar layanan
                $request->validate([
                    'nama' => [
                        'required',
                        'string',
                        'max:255',
                        // FIX: Mengubah 'layanans' menjadi 'layanan' (atau nama tabel yang benar)
                        Rule::unique('layanan', 'nama')->ignore($layanan->id)->where(
                            fn($query) =>
                            $query->where('unit_id', $request->unit_id)
                        ),
                    ],
                    'unit_id' => 'required|exists:units,id',
                    'prioritas' => 'nullable|integer',
                    'status_arsip' => 'required|in:0,1',
                ], [
                    'nama.unique' => 'Nama layanan sudah digunakan pada unit ini.',
                ]);

                // Perbarui data dasar Layanan
                $layanan->update([
                    'nama' => $request->nama,
                    'unit_id' => $request->unit_id,
                    'prioritas' => $request->prioritas ?? 0,
                    // Ambil nilai integer langsung dari select
                    'status_arsip' => (int) $request->status_arsip,
                ]);

                return redirect()->route('admin.layanan.edit', $layanan)
                    ->with('success', 'Data Dasar Layanan berhasil diperbarui.');
            }); // End DB::transaction

        } catch (\Exception $e) {
            // Tangkap error validasi atau database
            return redirect()->back()
                ->with('error', 'Gagal memperbarui layanan. Error: ' . $e->getMessage())
                ->withInput();
        }
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
