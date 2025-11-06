<?php

namespace App\Http\Controllers\AdminUnit; 

use App\Models\Tiket;
use App\Models\Unit;
use App\Models\KomentarTiket;
use Illuminate\Http\Request;
use App\Models\RiwayatStatusTiket;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TiketController extends Controller
{
    
    /**
     * Tampilkan SEMUA tiket, dengan opsi filter.
     */
    public function index(Request $request)
    {
        $query = Tiket::with(['mahasiswa.user', 'layanan', 'unit']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('prioritas')) {
            $query->where('prioritas', $request->prioritas);
        }
        if ($request->filled('id_unit')) {
            $query->where('id_unit', $request->id_unit);
        }

        $tiket = $query->latest()->paginate(10)->withQueryString();
        $units = Unit::orderBy('nama_unit', 'asc')->get();
        
        return view('admin_unit.dashboard', compact('tiket', 'units'));
    }

    /**
     * Tampilkan detail tiket.
     */
    public function show($id)
    {
        // !! INI PERBAIKANNYA !!
        // Mengganti 'riwayatStatus.diubahOleh' menjadi 'riwayatStatus.user'
        $tiket = Tiket::with(['mahasiswa.user', 'layanan', 'unit', 'komentar.user', 'riwayatStatus.user', 'detail'])
            ->findOrFail($id);
        
        return view('admin_unit.tiket.show', compact('tiket'));
    }

    /**
     * Update status/prioritas.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Dibuka,Sedang Dikerjakan,Ditutup,Selesai',
            'prioritas' => 'required|in:Rendah,Sedang,Tinggi',
        ]);

        $tiket = Tiket::findOrFail($id);
        
        $oldStatus = $tiket->status;
        $newStatus = $request->status;

        $tiket->update([
            'status' => $newStatus,
            'prioritas' => $request->prioritas,
        ]);

        if ($oldStatus !== $newStatus) {
            RiwayatStatusTiket::create([
                'id_tiket' => $tiket->id,
                'status_baru' => $newStatus,
                'diubah_oleh' => Auth::id(),
                'catatan' => 'Status diubah oleh Admin Unit.'
            ]);
        }

        return redirect()->route('admin_unit.tiket.show', $tiket->id)->with('success', 'Status tiket berhasil diperbarui.');
    }

    /**
     * Simpan komentar.
     */
    public function storeKomentar(Request $request, $id_tiket)
    {
        $request->validate([
            'isi_komentar' => 'required|string',
        ]);
        
        $tiket = Tiket::findOrFail($id_tiket);

        KomentarTiket::create([
            'id_tiket' => $tiket->id,
            'id_user' => Auth::id(),
            'isi_komentar' => $request->isi_komentar,
        ]);

        if ($tiket->status == 'Dibuka') {
            $tiket->update(['status' => 'Sedang Dikerjakan']);
            RiwayatStatusTiket::create([
                'id_tiket' => $tiket->id,
                'status_baru' => 'Sedang Dikerjakan',
                'diubah_oleh' => Auth::id(),
                'catatan' => 'Komentar ditambahkan oleh Admin Unit.'
            ]);
        }

        return redirect()->route('admin_unit.tiket.show', $tiket->id)->with('success', 'Komentar berhasil ditambahkan.');
    }
}