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
            return view('content.apps.kepala_unit.dashboard', ['unit' => null]);
        }
        $staffId = $staff->id;

        // 2. Kumpulkan ID Unit dan Layanan yang menjadi tanggung jawabnya
        $ledUnitIds = Unit::where('kepala_id', $staffId)->pluck('id')->toArray();
        $picLayananIds = LayananPenanggungJawab::where('staff_id', $staffId)->pluck('layanan_id')->toArray();

        // 3. Tentukan Unit Utama untuk Display
        $unit = Unit::where('kepala_id', $staffId)->first() ?? $staff->unit;

        // 4. Bangun Base Query untuk Tiket
        $baseQuery = Tiket::where(function ($query) use ($ledUnitIds, $picLayananIds) {

            if (!empty($ledUnitIds)) {
                $query->whereHas('layanan', function ($subQuery) use ($ledUnitIds) {
                    $subQuery->whereIn('unit_id', $ledUnitIds);
                });
            }

            if (!empty($picLayananIds)) {
                if (!empty($ledUnitIds)) {
                    $query->orWhereIn('layanan_id', $picLayananIds);
                } else {
                    $query->whereIn('layanan_id', $picLayananIds);
                }
            }
        });

        if (empty($ledUnitIds) && empty($picLayananIds)) {
            return view('content.apps.kepala_unit.dashboard', ['unit' => null]);
        }

        // 5. Hitung Metrik Dashboard (Logika Status Diperbaiki)

        $totalTiket = (clone $baseQuery)->count();

        // Tiket Sedang Diproses (Masih aktif ditangani)
        $tiketDiproses = (clone $baseQuery)->whereHas('statusTerbaru', function ($query) {
            $query->where('status', 'Ditangani_oleh_PIC');
        })->count();

        // Tiket Telah Selesai (Final, ditutup)
        $tiketSelesai = (clone $baseQuery)->whereHas('statusTerbaru', function ($query) {
            $query->whereIn('status', [
                'Dinilai_Selesai_oleh_Pemohon', // Selesai oleh Pemohon
                'Dinilai_Selesai_oleh_Kepala',  // Selesai oleh Kepala Unit
            ]);
        })->count();

        // Tiket Perlu Aksi Anda (Menunggu Keputusan Kepala Unit)
        $tiketPerluAksi = (clone $baseQuery)->whereHas('statusTerbaru', function ($query) {
            $query->whereIn('status', [
                // 1. PIC telah selesai dan menunggu penilaian Pemohon/Kepala Unit
                'Diselesaikan_oleh_PIC',

                // 2. Pemohon menolak penyelesaian, yang mungkin memerlukan intervensi Kepala Unit
                'Dinilai_Belum_Selesai_oleh_Pemohon',

                // 3. Masalah serius yang mungkin memerlukan intervensi Kepala Unit
                'Pemohon_Bermasalah',
            ]);
        })->count();

        // Ambil 5 Tiket Terbaru
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
