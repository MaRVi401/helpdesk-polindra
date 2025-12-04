<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use App\Models\KomentarTiket;
use App\Models\DetailTiketSuratKetAktif;
use App\Models\DetailTiketResetAkun;
use App\Models\DetailTiketUbahDataMhs;
use App\Models\DetailTiketReqPublikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class TiketApiController extends Controller
{
    /**
     * Middleware manual: cek API key.
     */
    private function checkApiKey(Request $request)
    {
        $clientKey = $request->header('API_KEY_MAHASISWA');
        $serverKey = env('API_KEY_MAHASISWA');

        if (!$clientKey || $clientKey !== $serverKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: API key tidak valid.'
            ], 401);
        }
        return null;
    }

    /**
     * GET /api/tiket/{id_or_no_tiket}
     */
    public function show(Request $request, $id_or_no_tiket)
    {
        if ($invalid = $this->checkApiKey($request)) {
            return $invalid;
        }

        // 1. Ambil Data Tiket Utama
        $tiket = Tiket::with([
            'layanan.unit',
            'unit',
            'detail', // Tetap load relasi ini (untuk data baru yang valid)
            'riwayatStatus.user',
            'komentar.pengirim',
            'pemohon.mahasiswa.programStudi.jurusan',
            'mahasiswa.programStudi.jurusan'
        ])
        ->where('id', $id_or_no_tiket)
        ->orWhere('no_tiket', $id_or_no_tiket)
        ->first();

        if (!$tiket) {
            return response()->json(['success' => false, 'message' => 'Tiket tidak ditemukan.'], 404);
        }

        // ============================================================================
        // FIX LOGIC: MANUAL FETCHING
        // Karena Seeder lama tidak mengisi 'detail_id' di tabel tiket, 
        // kita cari manual berdasarkan 'layanan_id' dan 'tiket_id'.
        // ============================================================================
        
        if ($tiket->detail === null) {
            $manualDetail = null;

            switch ($tiket->layanan_id) {
                case 1:
                    $manualDetail = DetailTiketSuratKetAktif::where('tiket_id', $tiket->id)->first();
                    break;
                case 2:
                    $manualDetail = DetailTiketResetAkun::where('tiket_id', $tiket->id)->first();
                    break;
                case 3:
                    $manualDetail = DetailTiketUbahDataMhs::where('tiket_id', $tiket->id)->first();
                    break;
                case 4:
                    $manualDetail = DetailTiketReqPublikasi::where('tiket_id', $tiket->id)->first();
                    break;
            }

            // Jika ketemu secara manual, pasang ke object tiket agar terbaca di Mobile (seolah-olah relasi)
            if ($manualDetail) {
                $tiket->setRelation('detail', $manualDetail);
            }
        }
        // ============================================================================

        // Ambil data timer (opsional)
        $deadline = null;
        if ($tiket->status == 'Diselesaikan_oleh_PIC') {
            $deadline = Cache::get('tiket_timer_' . $tiket->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail tiket ditemukan (with manual patch).',
            'data' => $tiket,
            'deadline_timer' => $deadline
        ]);
    }

    /**
     * POST /api/tiket/{id}/komentar
     */
    public function storeKomentar(Request $request, $id)
    {
        if ($invalid = $this->checkApiKey($request)) {
            return $invalid;
        }

        $tiket = Tiket::find($id);
        if (!$tiket) {
            return response()->json(['success' => false, 'message' => 'Tiket tidak ditemukan.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'komentar' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $komentar = new KomentarTiket();
        $komentar->tiket_id = $tiket->id;
        $komentar->pengirim_id = $tiket->pemohon_id;
        $komentar->komentar = $request->komentar;
        $komentar->save();

        return response()->json([
            'success' => true, 
            'message' => 'Komentar berhasil dikirim.',
            'data' => $komentar->load('pengirim')
        ]);
    }
}