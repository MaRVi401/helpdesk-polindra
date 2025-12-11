<?php

namespace App\Http\Controllers\AdminUnit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tiket;
use App\Models\Staff; 
use App\Models\Unit; 
use App\Models\Layanan; 

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->first();
        
        if (!$staff || !$staff->unit) {
            return view('content.apps.admin_unit.dashboard', ['unit' => null]);
        }

        $unit = $staff->unit; 
        $unitId = $unit->id;

        // 1. Ambil array mentah dari nama-nama layanan yang dikelola oleh Unit ini
        $layananNames = Layanan::where('unit_id', $unitId)->pluck('nama')->toArray();

        // Base Query (Tidak Berubah)
        $baseQuery = Tiket::whereHas('layanan', function ($query) use ($unitId) {
            $query->where('unit_id', $unitId);
        });

        // Hitung Metrik Dashboard (Tidak Berubah)
        $totalTiket = (clone $baseQuery)->count();
        $tiketBaru = (clone $baseQuery)->whereHas('statusTerbaru', function ($query) {
            $query->where('status', 'Diajukan_oleh_Pemohon');
        })->count();
        $tiketDiproses = (clone $baseQuery)->whereHas('statusTerbaru', function ($query) {
            $query->where('status', 'Ditangani_oleh_PIC');
        })->count();
        $tiketSelesai = (clone $baseQuery)->whereHas('statusTerbaru', function ($query) {
            $query->whereIn('status', ['Dinilai_Selesai_oleh_Pemohon', 'Dinilai_Selesai_oleh_Kepala']);
        })->count();
        
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
            'layananNames' // Variabel baru dikirim ke view
        ));
    }
}