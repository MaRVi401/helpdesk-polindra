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
    private function checkApiKey(Request $request)
    {
        $clientKey = $request->header('API_KEY_MAHASISWA');
        $serverKey = env('API_KEY_MAHASISWA', 'dalit123');

        if (!$clientKey || $clientKey !== $serverKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: API key tidak valid.'
            ], 401);
        }
        return null;
    }

    /**
     * GET /api/tiket/my-tickets
     * Mengambil semua tiket milik user yang sedang login
     */
    public function index(Request $request)
    {
        // Validasi Auth User (Dari Sanctum Token)
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized User'], 401);
        }

        // Ambil tiket dimana pemohon_id == user yang login
        $tikets = Tiket::with([
                'layanan',
                'unit',         
                'riwayatStatus' 
            ])
            ->where('pemohon_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

            $tikets->transform(function ($t) {
            $unitName = 'N/A';
            if ($t->unit) {
                $unitName = $t->unit->nama_unit;
            } elseif ($t->layanan && $t->layanan->unit) {
                $unitName = $t->layanan->unit->nama_unit;
            }
            $t->nama_unit_fixed = $unitName; 
            $latest = $t->riwayatStatus->first();
            $t->status_fixed = $latest ? $latest->status : $t->status;
            
            return $t;
        });

        foreach ($tikets as $tiket) {
            $latestStatus = $tiket->riwayatStatus->first(); 
            if ($latestStatus) {
                $tiket->status_terbaru = $latestStatus->status;
            } else {
                $tiket->status_terbaru = $tiket->status;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'List tiket berhasil diambil.',
            'data' => $tikets
        ]);
    }

    /**
     * GET /api/tiket/{id_or_no_tiket}
     */
    public function show(Request $request, $id_or_no_tiket)
    {
        if ($invalid = $this->checkApiKey($request)) {
            return $invalid;
        }

        $tiket = Tiket::with([
            'layanan.unit',
            'unit',
            'detail',
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
        
        if ($tiket->detail === null) {
            $manualDetail = null;
            switch ($tiket->layanan_id) {
                case 1: $manualDetail = DetailTiketSuratKetAktif::where('tiket_id', $tiket->id)->first(); break;
                case 2: $manualDetail = DetailTiketResetAkun::where('tiket_id', $tiket->id)->first(); break;
                case 3: $manualDetail = DetailTiketUbahDataMhs::where('tiket_id', $tiket->id)->first(); break;
                case 4: $manualDetail = DetailTiketReqPublikasi::where('tiket_id', $tiket->id)->first(); break;
            }
            if ($manualDetail) {
                $tiket->setRelation('detail', $manualDetail);
            }
        }

        // Ambil data timer
        $deadline = null;
        if ($tiket->status == 'Diselesaikan_oleh_PIC') {
            $deadline = Cache::get('tiket_timer_' . $tiket->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail tiket ditemukan.',
            'data' => $tiket,
            'deadline_timer' => $deadline
        ]);
    }

    /**
     * POST /api/tiket/{id}/komentar
     */
    public function storeKomentar(Request $request, $id)
    {
        // API Key Check (Layer 1)
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
        
        // GUNAKAN ID DARI TOKEN JIKA ADA, JIKA TIDAK GUNAKAN PEMOHON TIKET (Fallback)
        $komentar->pengirim_id = $request->user()->id ?? $tiket->pemohon_id;
        
        $komentar->komentar = $request->komentar;
        $komentar->save();

        return response()->json([
            'success' => true, 
            'message' => 'Komentar berhasil dikirim.',
            'data' => $komentar->load('pengirim')
        ]);
    }
}