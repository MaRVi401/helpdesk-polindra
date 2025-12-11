<?php

namespace App\Http\Controllers\AdminUnit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tiket;
use App\Models\Staff; 
use App\Models\Unit; 
use App\Models\Layanan; 
use App\Models\LayananPenanggungJawab; // Diperlukan untuk query PIC

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil ID Staff pengguna yang sedang login
        $staff = Staff::where('user_id', $user->id)->first();
        
        if (!$staff) {
            return view('content.apps.admin_unit.dashboard', ['unit' => null]);
        }
        $staffId = $staff->id;
        
        // 2. Kumpulkan Layanan ID di mana dia adalah PIC
        $picLayananIds = LayananPenanggungJawab::where('staff_id', $staffId)
                                                ->pluck('layanan_id')
                                                ->toArray();
        
        // 3. Tentukan Unit untuk Display (Ambil Unit tempat Staff terdaftar)
        // Walaupun logika tiket berbasis Layanan, kita tetap tampilkan unit tempat staff terdaftar di header.
        $unit = $staff->unit; 
        
        // --- BASE QUERY REVISI ---
        // 4. Base Query: Tiket yang Layanan ID-nya termasuk dalam Layanan ID PIC ini
        $baseQuery = Tiket::whereIn('layanan_id', $picLayananIds);
        
        // Jika tidak bertanggung jawab atas layanan apapun, kirim unit null (agar pesan peringatan muncul)
        if (empty($picLayananIds)) {
            return view('content.apps.admin_unit.dashboard', ['unit' => $unit, 'layananNames' => []]);
        }
        
        // 5. Ambil array mentah dari nama-nama layanan yang dikelola (PIC)
        $layananNames = Layanan::whereIn('id', $picLayananIds)->pluck('nama')->toArray();

        // 6. Hitung Metrik Dashboard (Menggunakan baseQuery yang sudah direvisi)
        
        $totalTiket = (clone $baseQuery)->count();
        
        // Status: Diajukan_oleh_Pemohon (Baru Masuk)
        $tiketBaru = (clone $baseQuery)->whereHas('statusTerbaru', function ($query) {
            $query->where('status', 'Diajukan_oleh_Pemohon');
        })->count();

        // Status: Ditangani_oleh_PIC (Sedang diproses)
        $tiketDiproses = (clone $baseQuery)->whereHas('statusTerbaru', function ($query) {
            $query->where('status', 'Ditangani_oleh_PIC');
        })->count();

        // Status: Selesai
        $tiketSelesai = (clone $baseQuery)->whereHas('statusTerbaru', function ($query) {
            $query->whereIn('status', ['Dinilai_Selesai_oleh_Pemohon', 'Dinilai_Selesai_oleh_Kepala']);
        })->count();
        
        // Ambil 5 Tiket Terbaru
        $latestTikets = (clone $baseQuery)
            ->with('statusTerbaru', 'pemohon', 'layanan') 
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('content.apps.admin_unit.dashboard', compact(
            'totalTiket', 
            'tiketBaru', 
            'tiketDiproses',
            'tiketSelesai', 
            'latestTikets',   
            'unit',
            'layananNames' // Mengirimkan nama layanan yang menjadi tanggung jawabnya
        ));
    }
}