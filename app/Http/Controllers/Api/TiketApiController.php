<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tiket;
use Illuminate\Http\Request;

class TiketApiController extends Controller
{
    /**
     * Middleware manual: cek API key sebelum lanjut.
     */
    private function checkApiKey(Request $request)
    {
        $clientKey = $request->header('API_KEY_MAHASISWA');
        $serverKey = env('API_KEY_MAHASISWA');

        if (!$clientKey || $clientKey !== $serverKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: API key tidak valid atau tidak dikirim.'
            ], 401);
        }

        return null; // valid
    }

    /**
     * GET /api/tiket/{id_or_no_tiket}
     * Ambil detail tiket berdasarkan ID atau No Tiket.
     */
    public function show(Request $request, $id_or_no_tiket)
    {
        // 🔒 Cek API key terlebih dahulu
        if ($invalid = $this->checkApiKey($request)) {
            return $invalid;
        }

        // 🔎 Cari tiket berdasarkan id atau no_tiket
        $tiket = Tiket::with(['layanan', 'riwayatStatus.user', 'komentar.pengirim'])
            ->where('id', $id_or_no_tiket)
            ->orWhere('no_tiket', $id_or_no_tiket)
            ->first();

        if (!$tiket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail tiket ditemukan.',
            'data' => $tiket
        ]);
    }
}
