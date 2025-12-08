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
        $statusFilter = $request->input('status');
        // $perPage tidak digunakan lagi karena kita tidak menggunakan pagination standar di view baru

        // 1. Ambil SEMUA Layanan
        $queryLayanan = Layanan::where('status_arsip', false)->with('unit');
        $data_layanan = $queryLayanan->get();

        $totalTiket = 0;
        $baseRoute = 'ticket.'; // Asumsi route Super Admin/Admin umum adalah 'ticket.index'

        // 2. Loop melalui setiap layanan dan ambil tiket yang sesuai dengan filter
        foreach ($data_layanan as $layanan) {
            // Query untuk tiket pada layanan ini
            $queryTiket = $layanan->tiket()->with('pemohon.mahasiswa', 'statusTerbaru');

            // --- Terapkan Filter Pencarian ---
            if ($searchQuery) {
                $queryTiket->where(function ($q) use ($searchQuery) {
                    $q->where('no_tiket', 'like', "%{$searchQuery}%")
                        ->orWhereHas('pemohon', function ($subQ) use ($searchQuery) {
                            $subQ->where('name', 'like', "%{$searchQuery}%");
                        });
                });
            }

            // --- Terapkan Filter Status ---
            if ($statusFilter) {
                $queryTiket->whereHas('statusTerbaru', function ($q) use ($statusFilter) {
                    $q->where('status', $statusFilter);
                });
            }

            // Ambil tiket yang sudah difilter dan masukkan kembali ke objek layanan
            // Gunakan get() karena kita ingin memproses seluruh koleksi sebelum ditampilkan
            $layanan->tiket = $queryTiket->latest()->get();
            $totalTiket += $layanan->tiket->count();
        }

        // 3. Hapus layanan yang tidak memiliki tiket setelah difilter (opsional, untuk tampilan lebih bersih)
        $data_layanan = $data_layanan->filter(fn($layanan) => $layanan->tiket->isNotEmpty());

        // Karena ini Super Admin, isPic diset true atau null/dihilangkan
        $isPic = null;

        // Ganti nama view jika Anda memisahkannya, atau pastikan view di bawah ini digunakan.
        return view('content.apps.admin.ticket.list', compact('data_layanan', 'totalTiket', 'isPic', 'baseRoute'));
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

        if ($namaLayanan && str_contains($namaLayanan, 'Surat Keterangan Aktif')) {
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

        // Inisialisasi variabel pesan
        $message = '';

        try {
            DB::transaction(function () use ($request, $tiket, &$message) {
                $statusSekarang = $tiket->statusTerbaru->status ?? 'Draft';
                $adminId = Auth::id();

                $isStatusUpdated = false;
                $isCommentAdded = false;

                // 1. Logika Tambah Komentar
                if ($request->filled('komentar')) {
                    KomentarTiket::create([
                        'tiket_id' => $tiket->id,
                        'pengirim_id' => $adminId,
                        'komentar' => $request->komentar,
                    ]);
                    $isCommentAdded = true;
                }

                // 2. Logika Update Status
                if ($request->filled('status') && $request->status != $statusSekarang) {
                    RiwayatStatusTiket::create([
                        'tiket_id' => $tiket->id,
                        'user_id' => $adminId,
                        'status' => $request->status,
                    ]);
                    $isStatusUpdated = true;
                }

                // Tentukan pesan sukses/info
                if ($isStatusUpdated && $isCommentAdded) {
                    $message = 'Status dan Komentar berhasil diperbarui.';
                } elseif ($isStatusUpdated) {
                    $message = 'Status berhasil diperbarui menjadi ' . str_replace('_', ' ', $request->status) . '.';
                } elseif ($isCommentAdded) {
                    $message = 'Komentar berhasil ditambahkan.';
                } else {
                    // Jika tidak ada perubahan
                    $message = 'Tidak ada perubahan yang dilakukan pada tiket.';
                }
            });

            // Pastikan ada sesuatu yang diubah.
            if (str_contains($message, 'Tidak ada perubahan')) {
                return redirect()->back()->with('error', $message);
            }

            // KEMBALIKAN REDIRECT DI LUAR CLOSURE
            return redirect()->back()->with('success', $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Log error untuk debugging lebih lanjut
            Log::error("Gagal memperbarui tiket: " . $e->getMessage() . " di baris " . $e->getLine());
            return redirect()->back()->with('error', 'Gagal memperbarui tiket: ' . $e->getMessage());
        }
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
