<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\DetailTiketReqPublikasi;
use App\Models\DetailTiketResetAkun;
use App\Models\DetailTiketSuratKetAktif;
use App\Models\DetailTiketUbahDataMhs;
use App\Models\KomentarTiket;
use App\Models\Layanan;
use App\Models\RiwayatStatusTiket;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TiketController extends Controller
{
    public function index()
    {
        $tikets = Tiket::where('pemohon_id', Auth::id())->with('layanan')->latest()->paginate(10);
        return view('mahasiswa.tiket.index', compact('tikets'));
    }

    public function create()
    {
        $layanans = Layanan::all();
        return view('mahasiswa.tiket.create', compact('layanans'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_layanan' => 'required|exists:layanan,id',
            'judul' => 'required|string|max:255',
            'prioritas' => 'required|in:Rendah,Sedang,Tinggi',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        try {
            DB::transaction(function () use ($request, $validatedData) {
                $layananId = $validatedData['id_layanan'];
                $detailTiket = null;
                $lampiranPath = null;

                if ($request->hasFile('lampiran')) {
                    $lampiranPath = $request->file('lampiran')->store('public/lampiran_tiket');
                }

                // PERBAIKAN: Menggunakan ID Layanan untuk logika, bukan slug
                // ID ini berdasarkan urutan di LayananSeeder Anda
                switch ($layananId) {
                    case 1: // Surat Keterangan Aktif Kuliah
                        $request->validate(['keperluan' => 'required|string|max:500']);
                        $detailTiket = DetailTiketSuratKetAktif::create(['keperluan' => $request->keperluan]);
                        break;
                    case 2: // Reset Akun
                        $request->validate(['alasan_reset' => 'required|string|max:500']);
                        $detailTiket = DetailTiketResetAkun::create(['alasan_reset' => $request->alasan_reset]);
                        break;
                    case 3: // Ubah Data Mahasiswa
                        $request->validate(['data_lama' => 'required|string|max:500', 'data_baru' => 'required|string|max:500']);
                        $detailTiket = DetailTiketUbahDataMhs::create($request->only(['data_lama', 'data_baru']));
                        break;
                    case 4: // Request Publikasi Event
                        $request->validate(['nama_event' => 'required|string|max:255', 'tanggal_event' => 'required|date']);
                        $detailTiket = DetailTiketReqPublikasi::create($request->only(['nama_event', 'tanggal_event', 'deskripsi_event']));
                        break;
                }

                $tiket = new Tiket();
                $tiket->no_tiket = 'TICKET-' . now()->format('Ymd-His') . '-' . rand(100, 999);
                $tiket->pemohon_id = Auth::id();
                $tiket->judul = $validatedData['judul'];
                $tiket->layanan_id = $layananId;
                $tiket->prioritas = $validatedData['prioritas'];
                $tiket->status = 'Menunggu';
                $tiket->lampiran = $lampiranPath;
                
                if ($detailTiket) {
                    $tiket->detail_tiketable()->associate($detailTiket);
                }
                $tiket->save();

                RiwayatStatusTiket::create([
                    'tiket_id' => $tiket->id,
                    'status' => 'Menunggu',
                    'user_id' => Auth::id(),
                    'catatan' => 'Tiket berhasil dibuat oleh pemohon.',
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat tiket: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('mahasiswa.tiket.index')->with('success', 'Tiket layanan berhasil diajukan.');
    }

    public function show(Tiket $tiket)
    {
        if ($tiket->pemohon_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat tiket ini.');
        }
        $tiket->load('layanan.unit', 'user', 'detail_tiketable', 'komentars.user', 'riwayatStatus.user');
        return view('mahasiswa.tiket.show', compact('tiket'));
    }

    public function storeKomentar(Request $request, Tiket $tiket)
    {
        if ($tiket->pemohon_id !== Auth::id()) {
            abort(403);
        }
        $request->validate(['isi_komentar' => 'required|string']);
        KomentarTiket::create([
            'tiket_id' => $tiket->id, 
            'user_id' => Auth::id(), 
            'isi_komentar' => $request->isi_komentar
        ]);
        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}

