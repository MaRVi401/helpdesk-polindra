<?php

namespace App\Http\Controllers\AdminUnit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\Tiket;
use App\Models\Staff;
use App\Models\Layanan;
use App\Models\RiwayatStatusTiket;
use App\Models\KomentarTiket;
use Carbon\Carbon;

class TiketController extends Controller
{
    private $validStatuses = [
        'Ditangani_oleh_PIC',
        'Diselesaikan_oleh_PIC',
        'Pemohon_Bermasalah',
    ];

    public function index(Request $request)
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->firstOrFail();
        $picLayananIds = $staff->layanan()->pluck('layanan.id')->toArray();

        if (empty($picLayananIds)) {
            return view('admin_unit.tiket.index', ['layanans' => collect([]), 'isPic' => false]);
        }

        $layanans = Layanan::whereIn('id', $picLayananIds)
            ->with(['tiket' => function($query) use ($request) {
                $query->with(['pemohon.mahasiswa', 'statusTerbaru'])->latest();
                
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
            }])
            ->get();

        $totalTiket = 0;
        foreach($layanans as $layanan) {
            $totalTiket += $layanan->tiket->count();
        }

        return view('admin_unit.tiket.index', compact('layanans', 'totalTiket'));
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

        // 1. Hitung berapa kali mahasiswa menolak (Status Merah: Dinilai_Belum_Selesai_oleh_Pemohon)
        $rejectionCount = $tiket->riwayatStatus()
            ->where('status', 'Dinilai_Belum_Selesai_oleh_Pemohon')
            ->count();

        // 2. Tentukan Status Saat Ini
        $currentStatus = $tiket->statusTerbaru?->status ?? 'Diajukan_oleh_Pemohon';

        // 3. Tentukan Opsi Status Selanjutnya (State Machine)
        $nextOptions = [];
        $isFormDisabled = false;
        $statusMessage = '';

        switch ($currentStatus) {
            case 'Diajukan_oleh_Pemohon':
                // Alur 1: Baru masuk -> Ditangani atau Tolak
                $nextOptions = [
                    'Ditangani_oleh_PIC' => '🛠️ Mulai Tangani Tiket',
                ];
                break;

            case 'Ditangani_oleh_PIC':
                // Alur 2: Sedang dikerjakan -> Selesaikan
                $nextOptions = [
                    'Diselesaikan_oleh_PIC' => '✅ Selesaikan (Kirim ke Pemohon)'
                ];
                break;

            case 'Diselesaikan_oleh_PIC':
                // Alur 4: Sudah selesai -> Disable (Tunggu Mahasiswa)
                $isFormDisabled = true;
                $statusMessage = 'Menunggu konfirmasi dari pemohon (Mahasiswa).';
                break;

            case 'Dinilai_Belum_Selesai_oleh_Pemohon':
                // Alur 6 & 7: Mahasiswa menolak
                $statusMessage = "Pemohon menilai tiket belum selesai (Penolakan ke-".($rejectionCount).").";
                
                // Opsi default: Tangani Lagi
                $nextOptions['Ditangani_oleh_PIC'] = '🛠️ Tangani Kembali (Revisi)';

                // Alur 8: Jika sudah lebih dari 2 kali (artinya ini yang ke-3 atau lebih), munculkan opsi Bermasalah
                if ($rejectionCount > 2) {
                    $nextOptions['Pemohon_Bermasalah'] = '⚠️ Tandai Pemohon Bermasalah';
                }
                break;
            
            case 'Pemohon_Bermasalah':
                // Admin bisa memutuskan untuk menangani lagi atau membiarkan
                $nextOptions['Ditangani_oleh_PIC'] = '🛠️ Coba Tangani Lagi';
                break;

            case 'Dinilai_Selesai_oleh_Pemohon':
            case 'Dinilai_Selesai_oleh_Kepala':
            case 'Ditolak':
                $isFormDisabled = true;
                $statusMessage = 'Tiket telah ditutup/selesai.';
                break;

            default:
                $nextOptions = ['Ditangani_oleh_PIC' => 'Reset ke Ditangani'];
                break;
        }

        // Load detail layanan
        $detailLayanan = $this->getDetailLayanan($tiket);

        return view('admin_unit.tiket.show', compact(
            'tiket', 
            'detailLayanan', 
            'nextOptions', 
            'isFormDisabled', 
            'statusMessage',
            'rejectionCount'
        ));
    }

    public function update(Request $request, $id)
    {
        $tiket = Tiket::findOrFail($id);
        $this->authorizeAccess($tiket);

        $request->validate([
            'komentar' => 'nullable|string',
            'status' => 'nullable|in:' . implode(',', $this->validStatuses),
        ]);

        try {
            DB::transaction(function () use ($request, $tiket) {
                $statusSekarang = $tiket->statusTerbaru->status ?? 'Diajukan_oleh_Pemohon';
                $adminId = Auth::id();

                // Komentar
                if ($request->filled('komentar')) {
                    KomentarTiket::create([
                        'tiket_id' => $tiket->id,
                        'pengirim_id' => $adminId,
                        'komentar' => $request->komentar,
                    ]);
                }

                // Update Status
                if ($request->filled('status') && $request->status != $statusSekarang) {
                    
                    // Validasi Alur (Backend Protection)
                    if ($request->status == 'Diselesaikan_oleh_PIC' && $statusSekarang == 'Diajukan_oleh_Pemohon') {
                         throw new \Exception('Tiket harus melalui proses "Ditangani" terlebih dahulu.');
                    }

                    // Cek limitasi Pemohon Bermasalah (Optional backend check)
                    if ($request->status == 'Pemohon_Bermasalah') {
                        $rejectionCount = $tiket->riwayatStatus()->where('status', 'Dinilai_Belum_Selesai_oleh_Pemohon')->count();
                        if ($rejectionCount <= 2) {
                            // throw new \Exception('Opsi ini hanya tersedia setelah 2 kali penolakan user.');
                            // Kita biarkan loose di backend, strict di frontend show() agar UX lebih fleksibel jika ada edge case
                        }
                    }

                    RiwayatStatusTiket::create([
                        'tiket_id' => $tiket->id,
                        'user_id' => $adminId,
                        'status' => $request->status,
                    ]);
                    $tiket->touch();
                }
            });

            return redirect()->route('admin_unit.tiket.show', $tiket->id)
                             ->with('success', 'Tiket berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    // ... (sisa method index, storeKomentar, authorizeAccess, checkAndProcessTimer, updateTimer, getDetailLayanan sama) ...
    // Pastikan helper getDetailLayanan ada di class ini
    private function getDetailLayanan($tiket) {
        $namaLayanan = $tiket->layanan->nama;
        if (Str::contains($namaLayanan, 'Surat Keterangan Aktif')) return $tiket->detailSuratKetAktif;
        if (Str::contains($namaLayanan, 'Reset Akun')) return $tiket->detailResetAkun;
        if (Str::contains($namaLayanan, 'Ubah Data')) return $tiket->detailUbahDataMhs;
        if (Str::contains($namaLayanan, 'Publikasi')) return $tiket->detailReqPublikasi;
        return null;
    }
    
    public function storeKomentar(Request $request, $id) { return $this->update($request, $id); }
    
    private function authorizeAccess($tiket) {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->first();
        $isPic = $staff->layanan()->where('layanan.id', $tiket->layanan_id)->exists();
        if (!$isPic) abort(403);
    }

    private function checkAndProcessTimer($tiket) {
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
            } else {
                $deadlineDate = Carbon::parse($deadline);
                if (Carbon::now()->greaterThan($deadlineDate)) {
                    Cache::forget($cacheKey); 
                    $this->autoCloseTicket($tiket);
                }
            }
        }
    }

    private function autoCloseTicket($tiket) {
        if ($tiket->statusTerbaru?->status !== 'Dinilai_Selesai_oleh_Pemohon') {
            RiwayatStatusTiket::create([
                'tiket_id' => $tiket->id,
                'user_id' => Auth::id(), 
                'status' => 'Dinilai_Selesai_oleh_Pemohon',
            ]);
            $tiket->touch(); 
        }
    }
    
    public function updateTimer(Request $request, $id) {
        $tiket = Tiket::findOrFail($id);
        $this->authorizeAccess($tiket);
        $request->validate(['amount' => 'required|integer|min:1', 'unit' => 'required|in:days,hours']);
        $cacheKey = 'tiket_timer_' . $tiket->id;
        $newDeadline = Carbon::now();
        if($request->unit == 'days') $newDeadline->addDays($request->amount);
        if($request->unit == 'hours') $newDeadline->addHours($request->amount);
        Cache::put($cacheKey, $newDeadline, now()->addYear());
        return back()->with('success', "Timer update.");
    }
}