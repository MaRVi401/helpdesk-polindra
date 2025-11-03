<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Models\Tiket;
use App\Models\Layanan;
use Illuminate\Http\Request;
use App\Models\KomentarTiket;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\DetailTiketResetAkun;
use App\Models\RiwayatStatusTiket;
use Illuminate\Support\Facades\Auth;
use App\Models\DetailTiketReqPublikasi;
use App\Models\DetailTiketSuratKetAktif;
use App\Models\DetailTiketUbahDataMhs;

class TiketController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    // Mengambil data tiket milik mahasiswa yang sedang login
    $tikets = Tiket::where('pemohon_id', Auth::id())
      ->with(['layanan', 'riwayatStatus']) // Load relasi yang diperlukan
      ->orderBy('created_at', 'desc')
      ->paginate(10); // Menggunakan paginasi

    // Menghitung total tiket
    $totalTiket = Tiket::where('pemohon_id', Auth::id())->count();

    // Mengirim data ke view
    return view('mahasiswa.tiket.index', compact('tikets', 'totalTiket'));
  }


  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $layanans = Layanan::where('status_arsip', false)->get();
    return view('mahasiswa.tiket.create', compact('layanans'));
  }

  /**
   * Store a new resource in storage.
   */
  public function store(Request $request)
  {
    // Validasi dasar
    $request->validate([
      'layanan_id' => 'required|exists:layanan,id',
      'deskripsi' => 'required|string',
    ]);

    // Memulai transaksi database
    DB::beginTransaction();
    try {
      // 1. Buat Tiket baru
      $tiket = Tiket::create([
        'no_tiket' => $this->generateNoTiket(),
        'pemohon_id' => Auth::id(),
        'layanan_id' => $request->layanan_id,
        'deskripsi' => $request->deskripsi,
      ]);

      // 2. Buat Riwayat Status Awal
      RiwayatStatusTiket::create([
        'tiket_id' => $tiket->id,
        'user_id' => Auth::id(),
        'status' => 'Pending',
      ]);

      // 3. Simpan detail tiket berdasarkan layanan
      $layanan = Layanan::find($request->layanan_id);

      switch ($layanan->nama) {
        case 'Surat Keterangan Aktif Kuliah':
          $request->validate([
            'keperluan' => 'required|string',
            'tahun_ajaran' => 'required|numeric',
            'semester' => 'required|integer',
          ]);
          DetailTiketSuratKetAktif::create([
            'tiket_id' => $tiket->id,
            'keperluan' => $request->keperluan,
            'tahun_ajaran' => $request->tahun_ajaran,
            'semester' => $request->semester,
            'keperluan_lainnya' => $request->keperluan_lainnya,
          ]);
          break;

        case 'Reset Akun E-Learning & Siakad':
          $request->validate(['aplikasi' => 'required|string']);
          DetailTiketResetAkun::create([
            'tiket_id' => $tiket->id,
            'aplikasi' => $request->aplikasi,
            'deskripsi' => $request->deskripsi_reset ?? 'Tidak ada deskripsi tambahan',
          ]);
          break;

        case 'Ubah Data Mahasiswa':
          $request->validate([
            'data_nama_lengkap' => 'required|string',
            'data_tmp_lahir' => 'required|string',
            'data_tgl_lhr' => 'required|date',
          ]);
          DetailTiketUbahDataMhs::create([
            'tiket_id' => $tiket->id,
            'data_nama_lengkap' => $request->data_nama_lengkap,
            'data_tmp_lahir' => $request->data_tmp_lahir,
            'data_tgl_lhr' => $request->data_tgl_lhr,
          ]);
          break;

        case 'Request Publikasi Event':
          $request->validate([
            'judul_event' => 'required|string',
            'kategori_event' => 'required|string',
            'konten_event' => 'required|string',
            'gambar_event' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
          ]);
          $gambarPath = null;
          if ($request->hasFile('gambar_event')) {
            $gambarPath = $request->file('gambar_event')->store('public/event_images');
          }
          DetailTiketReqPublikasi::create([
            'tiket_id' => $tiket->id,
            'judul' => $request->judul_event,
            'kategori' => $request->kategori_event,
            'konten' => $request->konten_event,
            'gambar' => $gambarPath,
          ]);
          break;
      }

      // Jika semua berhasil, commit transaksi
      DB::commit();

      return redirect()->route('mahasiswa.tiket.index')->with('success', 'Tiket berhasil dibuat.');
    } catch (\Exception $e) {
      // Jika terjadi error, rollback transaksi
      DB::rollBack();
      return back()->withInput()->withErrors(['error' => 'Gagal membuat tiket: ' . $e->getMessage()]);
    }
  }


  /**
   * Display the specified resource.
   */
  public function show(Tiket $tiket)
  {
    // Pastikan mahasiswa hanya bisa melihat tiket miliknya
    if ($tiket->pemohon_id !== Auth::id()) {
      abort(403);
    }

    // Eager load relasi
    $tiket->load([
      'layanan.unit',
      'pemohon', // <-- INI YANG DIPERBAIKI (dari 'user')
      'komentar.pengirim',
      'riwayatStatus.user'
    ]);

    // Mengirim data ke view
    return view('mahasiswa.tiket.show', compact('tiket'));
  }


  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Tiket $tiket)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Tiket $tiket)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Tiket $tiket)
  {
    //
  }

  /**
   * Generate a unique ticket number.
   */
  private function generateNoTiket()
  {
    // Format: YmdHis (e.g., 20251030173000)
    return date('YmdHis');
  }

  /**
   * Store a new comment.
   */
  public function storeKomentar(Request $request, Tiket $tiket)
  {
    $request->validate([
      'komentar' => 'required|string',
    ]);

    // Pastikan mahasiswa hanya bisa komentar di tiket miliknya
    if ($tiket->pemohon_id !== Auth::id()) {
      abort(403);
    }

    KomentarTiket::create([
      'tiket_id' => $tiket->id,
      'pengirim_id' => Auth::id(),
      'komentar' => $request->komentar,
    ]);

    return back()->with('success', 'Komentar berhasil dikirim.');
  }
}