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
use Illuminate\Support\Str;

class MonitoringTiketController extends Controller
{
    private $validStatuses = [
        'Dinilai_Selesai_oleh_Kepala',
    ];

    public function index(Request $request)
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->firstOrFail();
        
        $unitDipimpin = Unit::where('kepala_id', $staff->id)->first();
        $unitId = $unitDipimpin ? $unitDipimpin->id : null;
        $picLayananIds = $staff->layanan()->pluck('layanan.id')->toArray();

        $query = Tiket::with(['pemohon.mahasiswa.programStudi', 'layanan.unit', 'statusTerbaru'])
            ->where(function($mainQuery) use ($unitId, $picLayananIds) {
                if ($unitId) {
                    $mainQuery->whereHas('layanan', function ($q) use ($unitId) {
                        $q->where('unit_id', $unitId);
                    });
                }
                if (!empty($picLayananIds)) {
                    $mainQuery->orWhereIn('layanan_id', $picLayananIds);
                }
            })
            ->latest();

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
        if ($request->filled('prioritas')) {
            $prioInt = (int) $request->prioritas;
            $query->whereHas('layanan', fn($l) => $l->where('prioritas', $prioInt));
        }

        $tikets = $query->paginate(10)->withQueryString();
        return view('kepala_unit.monitoring_tiket.index', compact('tikets', 'unitDipimpin'));
    }

    public function show($id)
    {
        $tiket = Tiket::with([
            'pemohon.mahasiswa.programStudi.jurusan', 
            'layanan.unit', 
            'riwayatStatus.user', 
            'komentar.pengirim', 
            'detail'
        ])->findOrFail($id);
        
        $this->authorizeAccess($tiket);
        
        $detailLayanan = null;
        $namaLayanan = $tiket->layanan->nama;
        if (Str::contains($namaLayanan, 'Surat Keterangan Aktif')) $detailLayanan = $tiket->detailSuratKetAktif;
        elseif (Str::contains($namaLayanan, 'Reset Akun')) $detailLayanan = $tiket->detailResetAkun;
        elseif (Str::contains($namaLayanan, 'Ubah Data')) $detailLayanan = $tiket->detailUbahDataMhs;
        elseif (Str::contains($namaLayanan, 'Publikasi')) $detailLayanan = $tiket->detailReqPublikasi;

        return view('kepala_unit.monitoring_tiket.show', compact('tiket', 'detailLayanan'));
    }
    
    public function edit($id) {
        return $this->show($id);
    }
    public function update(Request $request, $id)
    {
        $tiket = Tiket::findOrFail($id);
        $this->authorizeAccess($tiket);
        $statusSaatIni = $tiket->statusTerbaru->status ?? null;
        if ($statusSaatIni !== 'Pemohon_Bermasalah') {
             return back()->with('error', 'Akses Ditolak: Anda hanya dapat memvalidasi tiket jika Admin/PIC telah mengubah status menjadi "Pemohon Bermasalah".');
        }
        $request->validate([
            'status' => 'required|in:Dinilai_Selesai_oleh_Kepala,Pemohon_Bermasalah,Diselesaikan_oleh_PIC',
        ]);

        $newStatus = $request->status;
        $tiket->touch();
        if ($statusSaatIni !== $newStatus) {
            RiwayatStatusTiket::create([
                'tiket_id' => $tiket->id,
                'user_id' => Auth::id(),
                'status' => $newStatus,
            ]);
        }

        return back()->with('success', 'Status tiket berhasil divalidasi.');
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

    private function authorizeAccess($tiket)
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->first();
        $unitDipimpin = Unit::where('kepala_id', $staff->id)->first();
        
        $isHead = $unitDipimpin && ($tiket->layanan->unit_id === $unitDipimpin->id);
        $isPic = $staff->layanan()->where('layanan.id', $tiket->layanan_id)->exists();

        if (!$isHead && !$isPic) {
            abort(403, 'Akses Ditolak.');
        }
    }
}