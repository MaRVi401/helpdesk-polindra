<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tiket;
use App\Models\Layanan;
use App\Models\User;
use App\Models\RiwayatStatusTiket;
use App\Models\KomentarTiket;
use App\Exports\TiketExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminTicketController extends Controller
{
    // Daftar status yang valid (sesuaikan jika perlu)
    private $validStatuses = [
        'Diajukan_oleh_Pemohon',
        'Ditangani_oleh_PIC',
        'Diselesaikan_oleh_PIC',
        'Dinilai_Belum_Selesai_oleh_Pemohon',
        'Pemohon_Bermasalah',
        'Dinilai_Selesai_oleh_Kepala',
        'Dinilai_Selesai_oleh_Pemohon',
    ];

    /**
     * Menampilkan daftar semua tiket (index).
     */
    public function index(Request $request)
    {
        $searchQuery = $request->input('q');
        $perPage = $request->input('per_page', 10);
        $statusFilter = $request->input('status');

        $query = Tiket::with(['pemohon', 'layanan.unit', 'statusTerbaru'])->latest();

        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('no_tiket', 'like', "%{$searchQuery}%")
                    ->orWhereHas('pemohon', function ($subQ) use ($searchQuery) {
                        $subQ->where('name', 'like', "%{$searchQuery}%");
                    })
                    ->orWhereHas('layanan', function ($subQ) use ($searchQuery) {
                        $subQ->where('nama', 'like', "%{$searchQuery}%");
                    })
                    ->orWhereHas('layanan.unit', function ($subQ) use ($searchQuery) {
                        $subQ->where('nama_unit', 'like', "%{$searchQuery}%");
                    });
            });
        }

        if ($statusFilter) {
            $query->whereHas('statusTerbaru', function ($q) use ($statusFilter) {
                $q->where('status', $statusFilter);
            });
        }

        $tikets = $query->paginate($perPage)->withQueryString();
        $statuses = $this->validStatuses;

        return view('content.apps.admin.ticket.list', compact('tikets', 'searchQuery', 'perPage', 'statusFilter', 'statuses'));
    }

    /**
     * Menampilkan form untuk membuat tiket baru (create).
     */
    public function create()
    {
        // Ambil hanya mahasiswa untuk pilihan pemohon
        $mahasiswas = User::where('role', 'mahasiswa')->get();
        $layanans = Layanan::where('status_arsip', false)->get();

        return view('content.apps.admin.ticket.create', compact('mahasiswas', 'layanans'));
    }

    /**
     * Menyimpan tiket baru ke database (store).
     */
    public function store(Request $request)
    {
        $request->validate([
            'pemohon_id' => 'required|exists:users,id',
            'layanan_id' => 'required|exists:layanan,id',
            'deskripsi' => 'required|string|min:10',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $tiket = Tiket::create([
                    'no_tiket' => $this->generateTicketNumber($request->layanan_id),
                    'pemohon_id' => $request->pemohon_id,
                    'layanan_id' => $request->layanan_id,
                    'deskripsi' => $request->deskripsi,
                ]);

                RiwayatStatusTiket::create([
                    'tiket_id' => $tiket->id,
                    'user_id' => Auth::id(),
                    'status' => 'Diajukan_oleh_Pemohon',
                ]);
            });

            return redirect()->route('ticket.index')->with('success', 'Tiket berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat tiket: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail tiket untuk diedit/dibalas (edit/show).
     */
    public function edit(Tiket $tiket)
    {
        $tiket->load([
            'pemohon.mahasiswa.programStudi.jurusan',
            'layanan.unit',
            'riwayatStatus.user',
            'komentar.pengirim'
        ]);

        $detailLayanan = null;
        $namaLayanan = $tiket->layanan->nama ?? null;

        if ($namaLayanan && str_contains($namaLayanan, 'Surat Keterangan Aktif Kuliah')) {
            $detailLayanan = $tiket->detailSuratKetAktif;
        } elseif ($namaLayanan && str_contains($namaLayanan, 'Reset Akun')) {
            $detailLayanan = $tiket->detailResetAkun;
        } elseif ($namaLayanan && str_contains($namaLayanan, 'Ubah Data Mahasiswa')) {
            $detailLayanan = $tiket->detailUbahDataMhs;
        } elseif ($namaLayanan && str_contains($namaLayanan, 'Request Publikasi')) {
            $detailLayanan = $tiket->detailReqPublikasi;
        }

        $statuses = $this->validStatuses;
        $statusSekarang = $tiket->statusTerbaru->status ?? 'Draft';

        return view('content.apps.admin.ticket.edit', compact('tiket', 'detailLayanan', 'statuses', 'statusSekarang'));
    }

    /**
     * Mengupdate status tiket atau menambah komentar (update).
     */
    public function update(Request $request, Tiket $tiket)
    {
        $request->validate([
            'komentar' => 'nullable|string|min:5',
            'status' => 'nullable|in:' . implode(',', $this->validStatuses),
        ]);

        try {
            DB::transaction(function () use ($request, $tiket) {
                $statusSekarang = $tiket->statusTerbaru->status ?? 'Draft';
                $adminId = Auth::id();

                if ($request->filled('komentar')) {
                    KomentarTiket::create([
                        'tiket_id' => $tiket->id,
                        'pengirim_id' => $adminId,
                        'komentar' => $request->komentar,
                    ]);
                }

                if ($request->filled('status') && $request->status != $statusSekarang) {
                    RiwayatStatusTiket::create([
                        'tiket_id' => $tiket->id,
                        'user_id' => $adminId,
                        'status' => $request->status,
                    ]);
                }
            });

            return redirect()->back()->with('success', 'Tiket berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui tiket: ' . $e->getMessage());
        }
    }

    /**
     * Handle ekspor data ke Excel.
     */
    public function exportExcel(Request $request)
    {
        $selectedIds = $request->input('selected_ids');

        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk diekspor.');
        }

        return Excel::download(new TiketExport($selectedIds), 'daftar_tiket_terpilih.xlsx');
    }



    private function getServiceCode($layananId)
    {
        $layanan = Layanan::find($layananId);

        if (is_null($layanan)) {
            return 'ERR';
        }

        $namaLayanan = $layanan->nama ?? 'Unknown';

        if (str_contains($namaLayanan, 'Surat Keterangan Aktif')) {
            return 'SKA';
        } elseif (str_contains($namaLayanan, 'Reset Akun')) {
            return 'RAM';
        } elseif (str_contains($namaLayanan, 'Ubah Data Mahasiswa')) {
            return 'UDM';
        } elseif (str_contains($namaLayanan, 'Request Publikasi')) {
            return 'RPK';
        } else {
            return 'TKT';
        }
    }
    private function generateTicketNumber($layananId)
    {
        $code = $this->getServiceCode($layananId);

        $date = now()->format('Ymd');

        $prefix = $code . '-' . $date . '%';

        $lastTicket = Tiket::where('no_tiket', 'like', $code . '-' . $date . '-%')
            ->orderBy('no_tiket', 'desc')
            ->first();

        if ($lastTicket) {
            $lastNumber = (int) substr($lastTicket->no_tiket, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        return $code . '-' . $date . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Menghapus tiket beserta semua relasinya secara permanen (destroy).
     */
    public function destroy(Tiket $tiket)
    {
        try {
            DB::transaction(function () use ($tiket) {
                $tiket->komentar()->delete();

                $tiket->riwayatStatus()->delete();

                if ($tiket->lampiran && Storage::disk('public')->exists($tiket->lampiran)) {
                    Storage::disk('public')->delete($tiket->lampiran);
                    Log::info("File lampiran umum berhasil dihapus: " . $tiket->lampiran);
                }


                if ($tiket->detailSuratKetAktif) {
                    $tiket->detailSuratKetAktif->delete();
                }

                if ($tiket->detailReqPublikasi) {
                    $detailPublikasi = $tiket->detailReqPublikasi;

                    if ($detailPublikasi->gambar) {
                        $filePath = $detailPublikasi->gambar;

                        if (Storage::disk('public')->exists($filePath)) {
                            $deleted = Storage::disk('public')->delete($filePath);
                            if ($deleted) {
                                Log::info("File publikasi berhasil dihapus: " . $filePath);
                            } else {
                                Log::error("Gagal menghapus file publikasi: " . $filePath . " (Izin Akses?)");
                            }
                        } else {
                            Log::warning("File publikasi tidak ditemukan di storage: " . $filePath);
                        }
                    }

                    $detailPublikasi->delete();
                }

                if ($tiket->detailResetAkun) {
                    $tiket->detailResetAkun->delete();
                }

                if ($tiket->detailUbahDataMhs) {
                    $tiket->detailUbahDataMhs->delete();
                }

                $tiket->delete();
            });

            return redirect()->route('ticket.index')->with('success', 'Tiket berhasil dihapus secara permanen. File lampiran telah dihapus.');
        } catch (\Exception $e) {
            Log::error("Gagal menghapus tiket/file: " . $e->getMessage() . " di baris " . $e->getLine());

            return redirect()->route('ticket.index')->with('error', 'Gagal menghapus tiket: ' . $e->getMessage());
        }
    }
}
