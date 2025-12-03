<?php
namespace App\Http\Controllers\Mahasiswa;
use App\Http\Controllers\Controller;
use App\Models\KomentarTiket;
use App\Models\Layanan;
use App\Models\RiwayatStatusTiket;
use App\Models\Tiket;
use App\Models\DetailTiketSuratKetAktif;
use App\Models\DetailTiketResetAkun;
use App\Models\DetailTiketUbahDataMhs;
use App\Models\DetailTiketReqPublikasi;
use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
    $data_tiket = Tiket::with(['layanan.unit', 'pemohon', 'statusAkhir'])
      ->where('pemohon_id', $userId)
      ->orderBy('created_at', 'desc')
      ->get();

    $total_tiket = $data_tiket->count();

    // Status yang dianggap selesai
    $statusSelesai = [
      'Dinilai_Selesai_oleh_Pemohon'
    ];

    // Hitung tiket yang status akhirnya termasuk status selesai
    $tiket_selesai = $data_tiket->filter(function ($tiket) use ($statusSelesai) {
      $statusTerbaru = $tiket->statusAkhir ? $tiket->statusAkhir->status : null;
      return $statusTerbaru && in_array($statusTerbaru, $statusSelesai);
    })->count();

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
    $userId = Auth::id();
    $total_tiket = Tiket::where('pemohon_id', $userId)->count();

    $data_layanan = Layanan::with('unit')
      ->where('status_arsip', 0)
      ->orderBy('nama', 'asc')
      ->get();

    return view('content.apps.mahasiswa.create', compact('data_layanan', 'total_tiket'), ['pageConfigs' => $this->pageConfigs]);
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

    // Load detail layanan
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

    // Tentukan status sekarang
    $riwayatTerbaru = $tiket->riwayatStatus->sortByDesc('created_at')->first();
    $statusSekarang = $riwayatTerbaru ? $riwayatTerbaru->status : 'Diajukan_oleh_Pemohon';

    $rejectionCount = $tiket->riwayatStatus()
      ->where('status', 'Dinilai_Belum_Selesai_oleh_Pemohon')
      ->count();

    $isFormDisabled = false;
    $statusMessage = '';
    $nextOptions = [];

    $closedStatuses = [
      'Dinilai_Selesai_oleh_Kepala',
      'Dinilai_Selesai_oleh_Pemohon',
      'Ditolak'
    ];

    if (in_array($statusSekarang, $closedStatuses)) {
      $isFormDisabled = true;
      $statusMessage = 'Tiket ini telah ditutup.';
    }

    if ($statusSekarang == 'Diselesaikan_oleh_PIC') {
      $statusMessage = 'Menunggu konfirmasi Anda untuk menyelesaikan tiket ini.';
    }

    return view('content.apps.mahasiswa.show', compact(
      'tiket',
      'detail',
      'statusSekarang',
      'rejectionCount',
      'isFormDisabled',
      'statusMessage',
      'nextOptions'
    ), ['pageConfigs' => $this->pageConfigs]);
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

    $data_tiket = Tiket::where('id', $id)
      ->where('pemohon_id', $userId)
      ->firstOrFail();

    if ($data_tiket->lampiran && Storage::disk('public')->exists($data_tiket->lampiran)) {
      Storage::disk('public')->delete($data_tiket->lampiran);
    }

    $detailPublikasi = DetailTiketReqPublikasi::where('tiket_id', $data_tiket->id)->first();
    if ($detailPublikasi && $detailPublikasi->gambar && Storage::disk('public')->exists($detailPublikasi->gambar)) {
      Storage::disk('public')->delete($detailPublikasi->gambar);
    }

    $data_tiket->delete();

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

      RiwayatStatusTiket::create([
        'tiket_id' => $tiket->id,
        'user_id' => Auth::id(),
        'status' => $newStatus,
      ]);

      $message = 'Status tiket berhasil diperbarui.';
      if ($newStatus == 'Dinilai_Selesai_oleh_Pemohon') {
        $message = 'Terima kasih! Tiket telah dinyatakan selesai.';
      } elseif ($newStatus == 'Dinilai_Belum_Selesai_oleh_Pemohon') {
        $message = 'Tiket dikembalikan ke PIC untuk penanganan ulang.';
      }

      return redirect()->back()->with('success', $message);
    }

    return redirect()->back()->with('error', 'Aksi tidak diizinkan untuk status tiket saat ini.');
  }

  public function updateTimer(Request $request, $id)
  {
    $tiket = Tiket::findOrFail($id);

    $request->validate(['amount' => 'required|integer|min:1', 'unit' => 'required|in:days,hours']);
    $cacheKey = 'tiket_timer_' . $tiket->id;
    $newDeadline = Carbon::now();
    if ($request->unit == 'days')
      $newDeadline->addDays($request->amount);
    if ($request->unit == 'hours')
      $newDeadline->addHours($request->amount);
    Cache::put($cacheKey, $newDeadline, now()->addYear());
    return back()->with('success', "Timer update.");
  }
}