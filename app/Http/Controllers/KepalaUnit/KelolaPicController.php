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
     * Menampilkan daftar layanan di unit yang dipimpin.
     */
    public function index()
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->firstOrFail();
        $unit = Unit::where('kepala_id', $staff->id)->firstOrFail();

        // Ambil layanan milik unit ini
        $layanans = Layanan::with(['penanggungJawab.user'])
                    ->where('unit_id', $unit->id)
                    ->orderBy('prioritas', 'asc')
                    ->get();

        return view('kepala_unit.kelola_pic.index', compact('unit', 'layanans'));
    }

    /**
     * Menampilkan halaman edit untuk layanan tertentu (Info Dasar + PIC).
     */
    public function edit($id)
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->firstOrFail();
        $unit = Unit::where('kepala_id', $staff->id)->firstOrFail();

        $layanan = Layanan::with(['penanggungJawab.user', 'unit'])->findOrFail($id);

        // Security: Pastikan layanan milik unit ini
        if ($layanan->unit_id !== $unit->id) {
            abort(403, 'Layanan ini bukan milik unit Anda.');
        }

        // Ambil staff yang SUDAH jadi PIC
        $currentPICS = $layanan->penanggungJawab;
        $currentPicIds = $currentPICS->pluck('id')->toArray();

        // Ambil SEMUA Staff Available (Lintas Unit) untuk dropdown tambah PIC
        // Exclude diri sendiri & yang sudah jadi PIC
        $availableStaff = Staff::with(['user', 'unit', 'jabatan'])
            ->where('id', '!=', $staff->id) 
            ->whereNotIn('id', $currentPicIds)
            ->whereHas('user', fn($q) => $q->where('role', '!=', 'super_admin'))
            ->get();

        return view('kepala_unit.kelola_pic.edit', compact('layanan', 'availableStaff', 'currentPICS', 'unit'));
    }

    /**
     * Menangani Update Info Layanan, Tambah PIC, dan Hapus PIC.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->first();
        $unit = Unit::where('kepala_id', $staff->id)->first();
        $layanan = Layanan::findOrFail($id);

        if($layanan->unit_id !== $unit->id) {
            abort(403, 'Akses Ditolak.');
        }

        try {
            return DB::transaction(function () use ($request, $layanan, $unit) {

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

                // 3. UPDATE INFO LAYANAN (Nama, Prioritas, Status)
                // Validasi hanya jika field nama ada (menandakan form update info yang disubmit)
                if ($request->has('nama')) {
                    $request->validate([
                        'nama' => [
                            'required', 'string', 'max:255',
                            Rule::unique('layanan', 'nama')->ignore($layanan->id)->where('unit_id', $unit->id)
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