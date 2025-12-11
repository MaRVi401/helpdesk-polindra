<?php

namespace App\Http\Controllers\KepalaUnit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tiket;
use App\Models\Unit;
use App\Models\Staff; 
use App\Models\LayananPenanggungJawab; 

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil ID Staff pengguna yang sedang login
        $staff = Staff::where('user_id', $user->id)->first();
        
        if (!$staff) {
            // Mengembalikan unit null untuk memicu pesan peringatan di view
            return view('content.apps.kepala_unit.dashboard', ['unit' => null]);
        }
        $staffId = $staff->id;

        // 2. Kumpulkan ID Unit dan Layanan yang menjadi tanggung jawabnya
        
        // A. Unit ID di mana dia adalah Kepala Unit
        $ledUnitIds = Unit::where('kepala_id', $staffId)->pluck('id')->toArray();
        
        // B. Layanan ID di mana dia adalah PIC
        $picLayananIds = LayananPenanggungJawab::where('staff_id', $staffId)->pluck('layanan_id')->toArray();
        
        // 3. Tentukan Unit Utama untuk Display (Digunakan di sambutan/header)
        // Ambil unit yang dipimpin, atau unit staff jika tidak memimpin unit
        $unit = Unit::where('kepala_id', $staffId)->first() ?? $staff->unit;

        // 4. Bangun Base Query untuk Tiket (Menggabungkan tanggung jawab A dan B)
        $baseQuery = Tiket::where(function ($query) use ($ledUnitIds, $picLayananIds) {
            
            // Filter 1: Tiket yang terkait dengan Unit yang dia pimpin
            if (!empty($ledUnitIds)) {
                $query->whereHas('layanan', function ($subQuery) use ($ledUnitIds) {
                    $subQuery->whereIn('unit_id', $ledUnitIds);
                });
            }

            // Filter 2: ATAU Tiket di Layanan yang dia tangani sebagai PIC
            if (!empty($picLayananIds)) {
                // Gunakan orWhereIn agar tiket dari PIC tetap terhitung meskipun bukan dari unit yang dipimpinnya
                if (!empty($ledUnitIds)) {
                     $query->orWhereIn('layanan_id', $picLayananIds);
                } else {
                     $query->whereIn('layanan_id', $picLayananIds);
                }
            }
        });

        // Jika tidak ada tanggung jawab sama sekali, kembalikan not found
        if (empty($ledUnitIds) && empty($picLayananIds)) {
             return view('content.apps.kepala_unit.dashboard', ['unit' => null]);
        }
        
        // 5. Hitung Metrik Dashboard menggunakan whereHas (memperbaiki QueryException)
        
        $totalTiket = (clone $baseQuery)->count();
        
        // Status: Ditangani_oleh_PIC (Sedang diproses)
        $tiketDiproses = (clone $baseQuery)->whereHas('statusTerbaru', function ($query) {
            $query->where('status', 'Ditangani_oleh_PIC');
        })->count();

        // Status: Dinilai_Selesai_oleh_Pemohon (Selesai Akhir)
        $tiketSelesai = (clone $baseQuery)->whereHas('statusTerbaru', function ($query) {
            $query->where('status', 'Dinilai_Selesai_oleh_Pemohon');
        })->count();

        // Status: Diselesaikan_oleh_PIC (Menunggu persetujuan/penilaian Kepala/Pemohon)
        $tiketPerluAksi = (clone $baseQuery)->whereHas('statusTerbaru', function ($query) {
            $query->where('status', 'Diselesaikan_oleh_PIC');
        })->count();

        // Ambil 5 Tiket Terbaru dengan relasi yang dibutuhkan
        $latestTikets = (clone $baseQuery)
            ->with('statusTerbaru', 'pemohon', 'layanan') 
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('content.apps.kepala_unit.dashboard', compact(
            'totalTiket', 
            'tiketDiproses', 
            'tiketSelesai',
            'tiketPerluAksi', 
            'latestTikets',   
            'unit' 
        ));
    }
}