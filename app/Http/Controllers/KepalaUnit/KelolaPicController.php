<?php

namespace App\Http\Controllers\KepalaUnit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Layanan;
use App\Models\Staff;
use App\Models\Unit;
use Illuminate\Validation\Rule;

class KelolaPicController extends Controller
{
    /**
     * Menampilkan daftar layanan di SEMUA unit yang dipimpin.
     */
    public function index()
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->firstOrFail();
        
        // UBAH: Ambil semua unit (get), bukan satu (first)
        $units = Unit::where('kepala_id', $staff->id)->get();

        // Ambil ID semua unit untuk query layanan
        $unitIds = $units->pluck('id')->toArray();

        // Ambil layanan milik SEMUA unit tersebut
        $layanans = Layanan::with(['penanggungJawab.user'])
                    ->whereIn('unit_id', $unitIds)
                    ->orderBy('prioritas', 'asc')
                    ->get();

        // Kirim variabel $units (jamak) ke view
        return view('content.apps.kepala_unit.kelola_pic.index', compact('units', 'layanans'));
    }

    /**
     * Menampilkan halaman edit untuk layanan tertentu.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->firstOrFail();
        
        // Ambil semua unit user untuk validasi
        $units = Unit::where('kepala_id', $staff->id)->get();
        $myUnitIds = $units->pluck('id')->toArray();

        $layanan = Layanan::with(['penanggungJawab.user', 'unit'])->findOrFail($id);

        // Security: Cek apakah layanan milik SALAH SATU unit user
        if (!in_array($layanan->unit_id, $myUnitIds)) {
            abort(403, 'Layanan ini bukan milik unit yang Anda pimpin.');
        }

        // Ambil staff yang SUDAH jadi PIC
        $currentPICS = $layanan->penanggungJawab;
        $currentPicIds = $currentPICS->pluck('id')->toArray();

        // Ambil SEMUA Staff Available (Lintas Unit)
        $availableStaff = Staff::with(['user', 'unit', 'jabatan'])
            ->where('id', '!=', $staff->id) 
            ->whereNotIn('id', $currentPicIds)
            ->whereHas('user', fn($q) => $q->where('role', '!=', 'super_admin'))
            ->get();

        // Kirim $unit spesifik milik layanan ini untuk breadcrumb/info di view edit
        $unit = $layanan->unit;

        return view('content.apps.kepala_unit.kelola_pic.edit', compact('layanan', 'availableStaff', 'currentPICS', 'unit'));
    }

    /**
     * Menangani Update Info Layanan, Tambah PIC, dan Hapus PIC.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->first();
        
        // Validasi multi unit
        $units = Unit::where('kepala_id', $staff->id)->get();
        $myUnitIds = $units->pluck('id')->toArray();

        $layanan = Layanan::findOrFail($id);

        if(!in_array($layanan->unit_id, $myUnitIds)) {
            abort(403, 'Akses Ditolak.');
        }

        try {
            return DB::transaction(function () use ($request, $layanan) {

                // 1. HAPUS PIC
                if ($request->filled('pic_id_to_remove')) {
                    $request->validate(['pic_id_to_remove' => 'required|exists:staff,id']);
                    $layanan->penanggungJawab()->detach($request->pic_id_to_remove);
                    
                    return redirect()->route('kepala-unit.pic.edit', $layanan->id)
                        ->with('success', 'PIC berhasil dihapus.');
                }

                // 2. TAMBAH PIC
                if ($request->filled('pic_id_to_add')) {
                    $request->validate(['pic_id_to_add' => 'required|exists:staff,id']);
                    $layanan->penanggungJawab()->attach($request->pic_id_to_add);

                    return redirect()->route('kepala-unit.pic.edit', $layanan->id)
                        ->with('success', 'PIC berhasil ditambahkan.');
                }

                // 3. UPDATE INFO LAYANAN
                if ($request->has('nama')) {
                    $request->validate([
                        'nama' => [
                            'required', 'string', 'max:255',
                            // Validasi unique scope unit_id layanan
                            Rule::unique('layanan', 'nama')->ignore($layanan->id)->where('unit_id', $layanan->unit_id)
                        ],
                        'prioritas' => 'required|integer|in:1,2,3',
                        'status_arsip' => 'required|in:0,1',
                    ]);
    
                    $layanan->update([
                        'nama' => $request->nama,
                        'prioritas' => $request->prioritas,
                        'status_arsip' => (int) $request->status_arsip,
                    ]);

                    return redirect()->route('kepala-unit.pic.edit', $layanan->id)
                        ->with('success', 'Informasi layanan diperbarui.');
                }
                
                return back();
            });

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}