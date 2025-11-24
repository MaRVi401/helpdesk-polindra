<?php

namespace App\Http\Controllers\KepalaUnit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tiket;
use App\Models\Staff;
use App\Models\Unit;
use App\Models\RiwayatStatusTiket;
use App\Models\KomentarTiket;

class MonitoringTiketController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->firstOrFail();
        
        // LOGIKA UTAMA (STRICT ASSIGNMENT):
        // Hanya ambil tiket dari layanan di mana staff ini (Kepala Unit) 
        // terdaftar secara EKSPLISIT sebagai Penanggung Jawab (PIC) di tabel pivot.
        // Jadi tiket yang tampil 100% bergantung pada assignment.
        
        $query = Tiket::with(['pemohon.mahasiswa.programStudi', 'layanan.unit', 'statusTerbaru'])
            ->whereHas('layanan.penanggungJawab', function($q) use ($staff) {
                $q->where('staff_id', $staff->id);
            })
            ->latest();

        // --- SEARCH & FILTERING ---
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('no_tiket', 'like', "%{$search}%")
                  ->orWhere('judul', 'like', "%{$search}%")
                  ->orWhereHas('pemohon', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('statusTerbaru', fn($q) => $q->where('status', $request->status));
        }

        $tikets = $query->paginate(10)->withQueryString();
        
        // Ambil data unit hanya untuk kebutuhan display header (opsional)
        $unitDipimpin = Unit::where('kepala_id', $staff->id)->first();

        return view('kepala_unit.monitoring_tiket.index', compact('tikets', 'unitDipimpin'));
    }

    public function show($id)
    {
        $tiket = Tiket::with(['pemohon.mahasiswa.programStudi.jurusan', 'layanan', 'riwayatStatus.user', 'komentar.user', 'detail'])
            ->findOrFail($id);
        
        $this->authorizeAccess($tiket);
        
        return view('kepala_unit.monitoring_tiket.show', compact('tiket'));
    }

    public function update(Request $request, $id)
    {
        $tiket = Tiket::findOrFail($id);
        $this->authorizeAccess($tiket);
        
        $request->validate(['status' => 'required|string']);

        RiwayatStatusTiket::create([
            'tiket_id' => $tiket->id,
            'user_id' => Auth::id(),
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status tiket berhasil diperbarui.');
    }

    public function storeKomentar(Request $request, $id)
    {
        $tiket = Tiket::findOrFail($id);
        $this->authorizeAccess($tiket);

        $request->validate(['komentar' => 'required|string']);

        KomentarTiket::create([
            'tiket_id' => $tiket->id,
            'pengirim_id' => Auth::id(),
            'komentar' => $request->komentar,
        ]);

        return back()->with('success', 'Komentar terkirim.');
    }

    // Helper: Validasi Hak Akses (Strict PIC Only)
    private function authorizeAccess($tiket)
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->first();
        
        // Cek apakah user ini benar-benar terdaftar sebagai PIC di layanan tiket tersebut
        $isPic = $staff->layanan()->where('layanan.id', $tiket->layanan_id)->exists();

        if (!$isPic) {
            abort(403, 'Akses Ditolak. Anda tidak terdaftar sebagai PIC untuk layanan tiket ini.');
        }
    }
}