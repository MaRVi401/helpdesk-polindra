<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Tiket;
use Illuminate\Http\Request;

class MahasiswaTicketController extends Controller
{
    /**
     * Mencari status tiket berdasarkan Nomor Tiket (no_tiket).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchTicket(Request $request)
    {
        $request->validate([
            'no_tiket' => 'required|integer',
        ]);

        $noTiket = $request->no_tiket;

        $tiket = Tiket::where('no_tiket', $noTiket)
            ->with([
                'pemohon.mahasiswa:user_id,nim',
                'layanan:id,nama',
                'riwayatStatusTiket' => function ($query) {
                    $query->latest()->select('tiket_id', 'status');
                }
            ])
            ->first();

        if (!$tiket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket dengan nomor ' . $noTiket . ' tidak ditemukan.'
            ], 404);
        }

        // Ambil status terbaru
        $currentStatus = $tiket->riwayatStatusTiket->pluck('status')->first() ?? 'Belum Diproses';

        $responseData = [
            'status' => 'success',
            'data' => [
                'no_tiket' => $tiket->no_tiket,
                'nim_pemohon' => $tiket->pemohon->mahasiswa->nim ?? 'N/A',
                'nama_pemohon' => $tiket->pemohon->name,
                'layanan' => $tiket->layanan->nama,
                'deskripsi_pengajuan' => $tiket->deskripsi,
                'status_terakhir' => $currentStatus,
                'tanggal_dibuat' => $tiket->created_at->format('Y-m-d H:i:s'),
                'detail_status' => $this->getStatusHistory($tiket),
            ]
        ];

        return response()->json($responseData, 200);
    }

    /**
     * Mendapatkan riwayat status untuk sebuah tiket.
     */
    private function getStatusHistory(Tiket $tiket)
    {
        return $tiket->riwayatStatusTiket
            ->map(function ($riwayat) {
                return [
                    'status' => $riwayat->status,
                    'waktu' => $riwayat->created_at->format('Y-m-d H:i:s'),
                ];
            })
            ->sortBy('waktu')
            ->values();
    }
}