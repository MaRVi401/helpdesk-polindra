<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KomentarTiket;
use App\Models\Layanan;
use App\Models\RiwayatStatusTiket;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\DetailTiketSuratKetAktif;
use App\Models\DetailTiketResetAkun;
use App\Models\DetailTiketUbahDataMhs;
use App\Models\DetailTiketReqPublikasi;

class ServiceTicketController extends Controller
{
  private $validStatuses = [
    'Diajukan_oleh_Pemohon',
    'Dinilai_Belum_Selesai_oleh_Pemohon',
    'Dinilai_Selesai_oleh_Pemohon',
  ];

  public function index()
  {
    $userId = Auth::id();

    // Ambil tiket dengan relasi yang dibutuhkan termasuk status terakhir
    $data_tiket = Tiket::with(['layanan.unit', 'pemohon', 'statusAkhir'])
      ->where('pemohon_id', $userId)
      ->orderBy('created_at', 'desc')
      ->get();

    // Total semua tiket
    $total_tiket = $data_tiket->count();

    // Status yang dianggap selesai
    $statusSelesai = [
      'Diselesaikan_oleh_PIC',
      'Dinilai_Selesai_oleh_Pemohon',
    ];

    // Hitung tiket yang status akhirnya termasuk status selesai
    $tiket_selesai = $data_tiket->filter(function ($tiket) use ($statusSelesai) {
      $statusTerbaru = $tiket->statusAkhir ? $tiket->statusAkhir->status : null;
      return $statusTerbaru && in_array($statusTerbaru, $statusSelesai);
    })->count();

    // Tiket belum selesai
    $belumSelesai = $total_tiket - $tiket_selesai;

    return view('content.apps.mahasiswa.list', compact(
      'data_tiket',
      'total_tiket',
      'tiket_selesai',
      'belumSelesai'
    ), ['pageConfigs' => $this->pageConfigs]);
  }


  public function create()
  {
    $data_layanan = Layanan::with('unit')
      ->where('status_arsip', 0)
      ->orderBy('nama', 'asc')
      ->get();
    return view('content.apps.mahasiswa.create', compact('data_layanan'), ['pageConfigs' => $this->pageConfigs]);
  }

  public function showCreateForm(Request $request)
  {
    $data_layanan = Layanan::with('unit')->get();
    $layananTerpilih = null;
    if ($request->has('layanan_id')) {
      $layananTerpilih = Layanan::find($request->layanan_id);
    }

    return view('content.apps.mahasiswa.create', compact('data_layanan', 'layananTerpilih'), ['pageConfigs' => $this->pageConfigs]);
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

    $data_layanan = Layanan::findOrFail($request->layanan_id);
    $namaLayanan = $data_layanan->nama ?? '';

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
    $tiketTerakhir = Tiket::where('no_tiket', 'like', $prefix . '-' . $date . '-%')
      ->orderBy('id', 'desc')
      ->first();

    $nomorUrut = 1;
    if ($tiketTerakhir) {
      $bagian = explode('-', $tiketTerakhir->no_tiket);
      $nomorUrutTerakhir = end($bagian);
      $nomorUrut = intval($nomorUrutTerakhir) + 1;
    }

    // Handle file upload untuk lampiran
    $lampiranPath = null;
    if ($request->hasFile('lampiran')) {
      $lampiranPath = $request->file('lampiran')->store('lampiran_tiket', 'public');
    }

    // Buat tiket baru
    $data_tiket = Tiket::create([
      'no_tiket' => $prefix . '-' . $date . '-' . str_pad($nomorUrut, 4, '0', STR_PAD_LEFT),
      'pemohon_id' => Auth::id(),
      'layanan_id' => $data_layanan->id,
      'deskripsi' => $request->deskripsi,
      'lampiran' => $lampiranPath,
    ]);

    RiwayatStatusTiket::create([
      'tiket_id' => $data_tiket->id,
      'user_id' => Auth::id(),
      'status' => 'Diajukan_oleh_Pemohon',
    ]);

    // Panggil method storeDetail
    $this->storeDetail($data_tiket->id, $data_layanan->nama, $request);
    return redirect()->route('service-ticket.index')
      ->with('success', 'Tiket Layanan berhasil dibuat.');
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

    return view('content.apps.mahasiswa.show', compact('tiket', 'detail', 'statusSekarang'), ['pageConfigs' => $this->pageConfigs])
      ->with('detailLayanan', $detail);
  }

  public function serviceTicketComment(Request $request, $id)
  {
    $request->validate([
      'komentar' => 'required|string',
    ]);

    $userId = Auth::id();
    $tiket = Tiket::where('id', $id)->where('pemohon_id', $userId)->firstOrFail();

    KomentarTiket::create([
      'tiket_id' => $tiket->id,
      'pengirim_id' => Auth::id(),
      'komentar' => $request->komentar,
    ]);

    return redirect()->route('service-ticket.show', $tiket->id)
      ->with('success', 'Komentar terkirim.');
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

    return redirect()->route('service-ticket.index')
      ->with('success', 'Tiket berhasil dihapus.');
  }
  public function statusConfirm(Request $request, $id)
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