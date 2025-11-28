<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KomentarTiket;
use App\Models\Layanan;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\DetailTiketSuratKetAktif;
use App\Models\DetailTiketResetAkun;
use App\Models\DetailTiketUbahDataMhs;
use App\Models\DetailTiketReqPublikasi;

class TiketController extends Controller
{
    private $validStatuses = [
        'Diajukan_oleh_Pemohon',
        'Dinilai_Belum_Selesai_oleh_Pemohon',
        'Dinilai_Selesai_oleh_Pemohon',
    ];

    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Tiket::where('pemohon_id', $userId)
            ->with(['layanan.unit', 'riwayatStatus', 'pemohon']);

        if ($request->has('status') && in_array($request->status, $this->validStatuses)) {
            $status = $request->status;
            $query->whereHas('riwayatStatus', function ($q) use ($status) {
                $q->where('status', $status)
                    ->whereRaw('id IN (SELECT MAX(id) FROM riwayat_status_tiket GROUP BY tiket_id)');
            });
        }

        if ($request->has('q') && $request->q != '') {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_tiket', 'like', "%$q%")
                    ->orWhere('deskripsi', 'like', "%$q%")
                    ->orWhereHas('layanan', function ($l) use ($q) {
                        $l->where('nama', 'like', "%$q%");
                    });
            });
        }

        $tikets = $query->latest()->paginate(10);
        $statuses = $this->validStatuses;

        return view('mahasiswa.tiket.index', compact('tikets', 'statuses'),['pageConfigs' => $this->pageConfigs]);
    }

    public function create()
    {
        $layanans = Layanan::with('unit')
            ->where('status_arsip', 0)
            ->orderBy('nama', 'asc')
            ->get();
        return view('mahasiswa.tiket.create', compact('layanans'),['pageConfigs' => $this->pageConfigs]);
    }

    public function showCreateForm(Request $request)
    {
        $layanans = Layanan::with('unit')->get();
        $layananTerpilih = null;

        if ($request->has('layanan_id')) {
            $layananTerpilih = Layanan::find($request->layanan_id);
        }

        return view('mahasiswa.tiket.create', compact('layanans', 'layananTerpilih'), ['pageConfigs' => $this->pageConfigs]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'layanan_id' => 'required|exists:layanan,id',
            'deskripsi' => 'required|string',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'judul_publikasi' => 'nullable|string',
            'kategori' => 'nullable|string',
            'konten' => 'nullable|string',
        ]);

        $layanan = Layanan::findOrFail($request->layanan_id);

        DB::transaction(function () use ($request, $layanan) {
            $namaLayanan = $layanan->nama ?? '';

            if (str_contains($namaLayanan, 'Surat Keterangan Aktif')) {
                $prefix = 'SKA';
            } elseif (str_contains($namaLayanan, 'Reset Akun')) {
                $prefix = 'RAM';
            } elseif (str_contains($namaLayanan, 'Ubah Data Mahasiswa')) {
                $prefix = 'UDM';
            } elseif (str_contains($namaLayanan, 'Request Publikasi')) {
                $prefix = 'RPK';
            } else {
                $prefix = 'TKT';
            }

            $date = now()->format('Ymd');
            $lastTiket = Tiket::where('no_tiket', 'like', $prefix . '-' . $date . '-%')
                ->orderBy('id', 'desc')
                ->first();

            $seq = 1;
            if ($lastTiket) {
                $parts = explode('-', $lastTiket->no_tiket);
                $lastSeqStr = end($parts);
                $seq = intval($lastSeqStr) + 1;
            }

            $tiket = new Tiket();
            $tiket->no_tiket = $prefix . '-' . $date . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
            $tiket->pemohon_id = Auth::id();
            $tiket->layanan_id = $layanan->id;
            $tiket->deskripsi = $request->deskripsi;

            if ($request->hasFile('lampiran')) {
                $tiket->lampiran = $request->file('lampiran')->store('lampiran_tiket', 'public');
            }

            $tiket->save();

            DB::table('riwayat_status_tiket')->insert([
                'tiket_id' => $tiket->id,
                'user_id' => Auth::id(),
                'status' => 'Diajukan_oleh_Pemohon',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->storeDetail($tiket->id, $layanan->nama, $request);
        });

        return redirect()->route('mahasiswa.tiket.index')
            ->with('success', 'Tiket berhasil dibuat.');
    }

    private function storeDetail($tiketId, $namaLayanan, $request)
    {
        if (str_contains($namaLayanan, 'Surat Keterangan Aktif Kuliah')) {
            DetailTiketSuratKetAktif::create([
                'tiket_id' => $tiketId,
                'keperluan' => $request->keperluan,
                'tahun_ajaran' => $request->tahun_ajaran,
                'semester' => $request->semester,
                'keperluan_lainnya' => $request->keperluan_lainnya,
            ]);
        } elseif (str_contains($namaLayanan, 'Reset Akun')) {
            DetailTiketResetAkun::create([
                'tiket_id' => $tiketId,
                'aplikasi' => $request->aplikasi,
                'deskripsi' => $request->deskripsi_detail ?? $request->deskripsi,
            ]);
        } elseif (str_contains($namaLayanan, 'Ubah Data Mahasiswa')) {
            DetailTiketUbahDataMhs::create([
                'tiket_id' => $tiketId,
                'data_nama_lengkap' => $request->data_nama_lengkap,
                'data_tmp_lahir' => $request->data_tmp_lahir,
                'data_tgl_lhr' => $request->data_tgl_lhr,
            ]);
        } elseif (str_contains($namaLayanan, 'Request Publikasi')) {
            $gambarPath = null;
            if ($request->hasFile('gambar')) {
                $gambarPath = $request->file('gambar')->store('lampiran-req-publikasi', 'public');
            }
            $detail = new DetailTiketReqPublikasi();
            $detail->tiket_id = $tiketId;
            $detail->judul = $request->judul_publikasi;
            $detail->kategori = $request->kategori;
            $detail->konten = $request->konten;
            $detail->gambar = $gambarPath;
            $detail->save();
        }
    }

    public function show($id)
    {
        $userId = Auth::id();

        $tiket = Tiket::where('id', $id)
            ->where('pemohon_id', $userId)
            ->with([
                'layanan.unit',
                'komentar.pengirim',
                'riwayatStatus.user',
                'pemohon.mahasiswa.programStudi.jurusan'
            ])
            ->firstOrFail();

        $detail = null;
        if ($tiket->detailSuratAktif)
            $detail = $tiket->detailSuratAktif;
        elseif ($tiket->detailResetAkun)
            $detail = $tiket->detailResetAkun;
        elseif ($tiket->detailUbahData)
            $detail = $tiket->detailUbahData;
        elseif ($tiket->detailPublikasi)
            $detail = $tiket->detailPublikasi;

        if (!$detail) {
            if (str_contains($tiket->layanan->nama, 'Surat Keterangan Aktif Kuliah'))
                $detail = DetailTiketSuratKetAktif::where('tiket_id', $tiket->id)->first();
            elseif (str_contains($tiket->layanan->nama, 'Reset Akun'))
                $detail = DetailTiketResetAkun::where('tiket_id', $tiket->id)->first();
            elseif (str_contains($tiket->layanan->nama, 'Ubah Data Mahasiswa'))
                $detail = DetailTiketUbahDataMhs::where('tiket_id', $tiket->id)->first();
            elseif (str_contains($tiket->layanan->nama, 'Request Publikasi'))
                $detail = DetailTiketReqPublikasi::where('tiket_id', $tiket->id)->first();
        }

        $riwayatTerbaru = $tiket->riwayatStatus->sortByDesc('created_at')->first();
        $statusSekarang = $riwayatTerbaru ? $riwayatTerbaru->status : 'Diajukan_oleh_Pemohon';

        return view('mahasiswa.tiket.show', compact('tiket', 'detail', 'statusSekarang'))
            ->with('detailLayanan', $detail);
    }

    public function storeKomentar(Request $request, $id)
    {
        $request->validate([
            'komentar' => 'required|string',
        ]);

        $userId = Auth::id();
        $tiket = Tiket::where('id', $id)->where('pemohon_id', $userId)->firstOrFail();

        $komentar = new KomentarTiket();
        $komentar->tiket_id = $tiket->id;
        $komentar->pengirim_id = Auth::id();
        $komentar->komentar = $request->komentar;
        $komentar->save();

        return redirect()->route('mahasiswa.tiket.show', $tiket->id)->with('success', 'Komentar terkirim.');
    }

    public function destroy($id)
    {
        $userId = Auth::id();

        $tiket = Tiket::where('id', $id)
            ->where('pemohon_id', $userId)
            ->firstOrFail();

        if ($tiket->lampiran && Storage::disk('public')->exists($tiket->lampiran)) {
            Storage::disk('public')->delete($tiket->lampiran);
        }

        $detailPublikasi = DetailTiketReqPublikasi::where('tiket_id', $tiket->id)->first();
        if ($detailPublikasi && $detailPublikasi->gambar && Storage::disk('public')->exists($detailPublikasi->gambar)) {
            Storage::disk('public')->delete($detailPublikasi->gambar);
        }

        $tiket->delete();

        return redirect()->route('mahasiswa.tiket.index')
            ->with('success', 'Tiket berhasil dihapus.');
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $userId = Auth::id();
        $tiket = Tiket::where('id', $id)->where('pemohon_id', $userId)->firstOrFail();
        $lastStatus = $tiket->riwayatStatus->sortByDesc('created_at')->first();

        if ($lastStatus && $lastStatus->status == 'Diselesaikan_oleh_PIC') {

            $newStatus = $request->status;
            if (!in_array($newStatus, ['Dinilai_Selesai_oleh_Pemohon', 'Dinilai_Belum_Selesai_oleh_Pemohon'])) {
                return redirect()->back()->with('error', 'Status tidak valid.');
            }

            DB::table('riwayat_status_tiket')->insert([
                'tiket_id' => $tiket->id,
                'user_id' => Auth::id(),
                'status' => $newStatus,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $msg = 'Status tiket berhasil diperbarui.';
            if ($newStatus == 'Ditangani_oleh_PIC') {
                $msg = 'Tiket dikembalikan ke PIC untuk penanganan ulang.';
            } elseif ($newStatus == 'Dinilai_Selesai_oleh_Pemohon') {
                $msg = 'Terima kasih! Tiket telah dinyatakan selesai.';
            }

            return redirect()->back()->with('success', $msg);
        }

        return redirect()->back()->with('error', 'Aksi tidak diizinkan untuk status tiket saat ini.');
    }
}