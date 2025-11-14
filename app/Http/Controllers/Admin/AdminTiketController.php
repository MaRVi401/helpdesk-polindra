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

class AdminTiketController extends Controller
{
    // Daftar status yang valid (sesuaikan jika perlu)
    private $validStatuses = ['Pending', 'Diproses', 'Selesai', 'Ditolak'];

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

        return view('admin.tiket.index', compact('tikets', 'searchQuery', 'perPage', 'statusFilter', 'statuses'));
    }

    /**
     * Menampilkan form untuk membuat tiket baru (create).
     */
    public function create()
    {
        // Ambil hanya mahasiswa untuk pilihan pemohon
        $mahasiswas = User::where('role', 'mahasiswa')->get();
        $layanans = Layanan::where('status_arsip', false)->get();

        return view('admin.tiket.create', compact('mahasiswas', 'layanans'));
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
                // 1. Buat Tiket
                $tiket = Tiket::create([
                    'no_tiket' => $this->generateTicketNumber($request->layanan_id),
                    'pemohon_id' => $request->pemohon_id,
                    'layanan_id' => $request->layanan_id,
                    'deskripsi' => $request->deskripsi,
                ]);

                // 2. Buat Riwayat Status Awal
                RiwayatStatusTiket::create([
                    'tiket_id' => $tiket->id,
                    'user_id' => Auth::id(), // Dibuat oleh Admin
                    'status' => 'Pending', // Status awal
                ]);

                // 3. (Opsional) Buat detail tiket
                // Ini memerlukan logika tambahan berdasarkan $request->layanan_id
                // Contoh:
                // if ($tiket->layanan->nama == 'Surat Keterangan Aktif') {
                //     DetailTiketSuratKetAktif::create([...]);
                // }
            });

            return redirect()->route('admin.tiket.index')->with('success', 'Tiket berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat tiket: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan detail tiket untuk diedit/dibalas (edit/show).
     */
    public function edit(Tiket $tiket)
    {
        // Load semua relasi yang diperlukan
        $tiket->load([
            'pemohon.mahasiswa.programStudi.jurusan',
            'layanan.unit',
            'riwayatStatus.user',
            'komentar.pengirim'
        ]);

        // Load detail spesifik (jika ada)
        // Anda perlu menyesuaikan ini berdasarkan nama layanan
        $detailLayanan = null;
        $namaLayanan = $tiket->layanan->nama;

        if ($namaLayanan == 'Surat Keterangan Aktif') {
            $detailLayanan = $tiket->detailSuratKetAktif;
        } elseif ($namaLayanan == 'Reset Akun') {
            $detailLayanan = $tiket->detailResetAkun;
        } elseif ($namaLayanan == 'Ubah Data Mahasiswa') {
            $detailLayanan = $tiket->detailUbahDataMhs;
        } elseif ($namaLayanan == 'Request Publikasi') {
            $detailLayanan = $tiket->detailReqPublikasi;
        }

        $statuses = $this->validStatuses;
        $statusSekarang = $tiket->statusTerbaru->status ?? 'Draft';

        return view('admin.tiket.edit', compact('tiket', 'detailLayanan', 'statuses', 'statusSekarang'));
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

                // 1. Cek jika ada komentar baru
                if ($request->filled('komentar')) {
                    KomentarTiket::create([
                        'tiket_id' => $tiket->id,
                        'pengirim_id' => $adminId,
                        'komentar' => $request->komentar,
                    ]);
                }

                // 2. Cek jika ada perubahan status
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
        $namaLayanan = $layanan->nama ?? 'Unknown';

        // Logika kustom untuk membuat kode 3 huruf dari nama layanan
        if (str_contains($namaLayanan, 'Surat Keterangan Aktif')) {
            return 'SKA';
        } elseif (str_contains($namaLayanan, 'Reset Akun')) {
            return 'RMA';
        } elseif (str_contains($namaLayanan, 'Ubah Data Mahasiswa')) {
            return 'UDM';
        } elseif (str_contains($namaLayanan, 'Request Publikasi')) {
            return 'RPK';
        } else {
            // Fallback code jika tidak cocok dengan kriteria di atas
            return 'TKT';
        }
    }
    private function generateTicketNumber($layananId)
    {
        // Ambil Kode Layanan
        $code = $this->getServiceCode($layananId);

        // Format tanggal: YYYYMMDD (misal: 20251114)
        $date = now()->format('Ymd');

        // Prefix untuk pencarian di hari ini
        $prefix = $code . '-' . $date . '%';

        // Cari tiket terakhir dengan kode dan tanggal hari ini
        $lastTicket = Tiket::where('no_tiket', 'like', $code . '-' . $date . '-%')
            ->orderBy('no_tiket', 'desc')
            ->first();

        if ($lastTicket) {
            // Ambil nomor urut terakhir (4 digit setelah strip terakhir)
            $lastNumber = (int) substr($lastTicket->no_tiket, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format akhir: SKA-YYYYMMDD-XXXX (misal: SKA-20251114-0001)
        return $code . '-' . $date . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
