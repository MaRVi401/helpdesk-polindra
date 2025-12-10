<?php

namespace App\Http\Controllers\KepalaUnit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Tiket;
use App\Models\Staff;
use App\Models\Unit;
use App\Models\RiwayatStatusTiket;
use App\Models\KomentarTiket;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MonitoringTiketController extends Controller
{
    private $validStatuses = [
        'Dinilai_Selesai_oleh_Kepala',
    ];

    public function index(Request $request)
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->firstOrFail();
        
        // Ambil SEMUA unit yang dipimpin
        $unitsDipimpin = Unit::where('kepala_id', $staff->id)->get();
        
        // Buat array ID unit
        $unitIds = $unitsDipimpin->isNotEmpty() ? $unitsDipimpin->pluck('id')->toArray() : [];
        
        $picLayananIds = $staff->layanan()->pluck('layanan.id')->toArray();

        $query = Tiket::with(['pemohon.mahasiswa.programStudi', 'layanan.unit', 'statusTerbaru'])
            ->where(function($mainQuery) use ($unitIds, $picLayananIds) {
                // Logika OR: Jika dia Kepala Unit (cek array unitIds) ATAU dia PIC
                if (!empty($unitIds)) {
                    $mainQuery->whereHas('layanan', function ($q) use ($unitIds) {
                        $q->whereIn('unit_id', $unitIds);
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
                  ->orWhere('deskripsi', 'like', "%{$search}%")
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
        
        return view('kepala_unit.monitoring_tiket.index', compact('tikets', 'unitsDipimpin'));
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
        $this->checkAndProcessTimer($tiket);

        $cacheKey = 'tiket_timer_' . $tiket->id;
        $tiket->cached_deadline = Cache::get($cacheKey);
        
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

        // PERBAIKAN: Gunakan null safe operator (?->)
        $statusSaatIni = $tiket->statusTerbaru?->status;

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
        
        $unitsDipimpin = Unit::where('kepala_id', $staff->id)->get();
        
        // Cek apakah tiket berasal dari SALAH SATU unit yang dipimpin
        $isHead = $unitsDipimpin->contains('id', $tiket->layanan->unit_id);
        
        // Cek PIC
        $isPic = $staff->layanan()->where('layanan.id', $tiket->layanan_id)->exists();

        if (!$isHead && !$isPic) {
            abort(403, 'Akses Ditolak.');
        }
    }

    private function checkAndProcessTimer($tiket)
    {
        // PERBAIKAN: Gunakan null safe operator (?->) untuk mencegah error jika statusTerbaru null
        if ($tiket->statusTerbaru?->status === 'Diselesaikan_oleh_PIC') {
            
            $cacheKey = 'tiket_timer_' . $tiket->id;
            $deadline = Cache::get($cacheKey);
            if (!$deadline) {
                $baseTime = $tiket->updated_at;
                $defaultDeadline = $baseTime->copy()->addDays(7);

                if (Carbon::now()->greaterThan($defaultDeadline)) {
                    $this->autoCloseTicket($tiket);
                } else {
                    Cache::put($cacheKey, $defaultDeadline, now()->addYear()); 
                }
            }
            else {
                $deadlineDate = Carbon::parse($deadline);
                if (Carbon::now()->greaterThan($deadlineDate)) {
                    Cache::forget($cacheKey); 
                    $this->autoCloseTicket($tiket);
                }
            }
        } else {
            Cache::forget('tiket_timer_' . $tiket->id);
        }
    }
    
    private function autoCloseTicket($tiket) {
        // PERBAIKAN: Gunakan null safe operator (?->)
        if ($tiket->statusTerbaru?->status !== 'Dinilai_Selesai_oleh_Pemohon') {
            RiwayatStatusTiket::create([
                'tiket_id' => $tiket->id,
                'user_id' => Auth::id(),
                'status' => 'Dinilai_Selesai_oleh_Pemohon',
            ]);
            $tiket->touch(); 
        }
    }

    public function updateTimer(Request $request, $id)
    {
        $tiket = Tiket::findOrFail($id);
        $this->authorizeAccess($tiket);
        $request->validate([
            'amount' => 'required|integer|min:1',
            'unit' => 'required|in:seconds,minutes,hours,days',
        ]);

        $cacheKey = 'tiket_timer_' . $tiket->id;
        $amount = (int) $request->amount; 
        $unit = $request->unit;
        $tiket->touch(); 
        $newDeadline = Carbon::now();
        
        switch ($unit) {
            case 'seconds': $newDeadline->addSeconds($amount); break;
            case 'minutes': $newDeadline->addMinutes($amount); break;
            case 'hours':   $newDeadline->addHours($amount); break;
            case 'days':    $newDeadline->addDays($amount); break;
        }
        Cache::put($cacheKey, $newDeadline, now()->addYear());

        return back()->with('success', "Timer berhasil di-reset menjadi $amount " . ucfirst($unit));
    }
}