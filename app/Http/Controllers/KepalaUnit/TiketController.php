<?php

namespace App\Http\Controllers\KepalaUnit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tiket;
use App\Models\Staff;
use App\Models\Unit;

class TiketController extends Controller
{
    /**
     * Menampilkan daftar tiket yang HANYA terkait dengan Unit yang dipimpin user login.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->firstOrFail();

        // 1. Identifikasi Unit yang dipimpin
        $unitDipimpin = Unit::where('kepala_id', $staff->id)->first();

        if (!$unitDipimpin) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda bukan Kepala Unit.');
        }

        // 2. Query Strict: Hanya ambil tiket yang layanannya milik unit ini
        $query = Tiket::with(['mahasiswa.user', 'layanan', 'statusTerbaru', 'pic.user'])
            ->whereHas('layanan', function ($q) use ($unitDipimpin) {
                $q->where('unit_id', $unitDipimpin->id);
            });

        // 3. Filter Tambahan
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('no_tiket', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
             // Asumsi status denormalisasi ada di tabel tiket atau via relasi riwayat
             // Jika menggunakan RiwayatStatusTiket, gunakan whereHas('statusTerbaru', ...)
             $query->whereHas('statusTerbaru', function($q) use ($request){
                 $q->where('status', $request->status);
             });
        }

        $tikets = $query->latest()->paginate(10);

        return view('kepala_unit.tiket.index', compact('tikets', 'unitDipimpin'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->firstOrFail();
        $unitDipimpin = Unit::where('kepala_id', $staff->id)->firstOrFail();

        $tiket = Tiket::with(['mahasiswa.user', 'layanan.unit', 'komentar.user', 'riwayatStatus.user', 'detail'])
            ->findOrFail($id);

        // SECURITY CHECK: Pastikan tiket ini milik unit yang dipimpin
        if ($tiket->layanan->unit_id !== $unitDipimpin->id) {
            abort(403, 'Tiket ini berasal dari layanan unit lain ('.$tiket->layanan->unit->nama_unit.'). Anda tidak memiliki akses.');
        }
        
        return view('kepala_unit.tiket.show', compact('tiket'));
    }
}