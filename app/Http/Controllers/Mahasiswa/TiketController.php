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
        $tikets = Tiket::where('pemohon_id', Auth::id())->with('layanan', 'riwayatStatus')->latest()->paginate(10);
        return view('mahasiswa.tiket.index', compact('tikets'));
    }

    public function create()
    {
        $layanans = Layanan::where('status_arsip', true)->get();
        return view('mahasiswa.tiket.create', compact('layanans'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'layanan_id' => 'required|exists:layanan,id',
            'deskripsi' => 'required|string|min:10',
        ]);

        try {
            DB::transaction(function () use ($request, $validatedData) {
                
                $tiket = Tiket::create([
                    'no_tiket' => rand(100000, 999999),
                    'pemohon_id' => Auth::id(),
                    'layanan_id' => $validatedData['layanan_id'],
                    'deskripsi' => $validatedData['deskripsi'],
                ]);
                
                RiwayatStatusTiket::create([
                    'tiket_id' => $tiket->id,
                    'user_id' => Auth::id(),
                    'status' => 'Draft',
                ]);
                
                $layanan = Layanan::find($validatedData['layanan_id']);
                
                switch ($layanan->nama) {
                    case 'Surat Keterangan Aktif Kuliah':
                        $request->validate([
                            'keperluan' => 'required', 'tahun_ajaran' => 'required|digits:4', 'semester' => 'required|integer|min:1|max:14'
                        ]);
                        DetailTiketSuratKetAktif::create([
                            'tiket_id' => $tiket->id, 
                            'keperluan' => $request->keperluan,
                            'tahun_ajaran' => $request->tahun_ajaran,
                            'semester' => $request->semester,
                            'keperluan_lainnya' => $request->keperluan_lainnya
                        ]);
                        break;
                    case 'Reset Akun E-Learning & Siakad':
                        $request->validate(['aplikasi' => 'required', 'deskripsi_reset' => 'required']);
                        DetailTiketResetAkun::create([
                            'tiket_id' => $tiket->id, 
                            'aplikasi' => $request->aplikasi,
                            'deskripsi' => $request->deskripsi_reset
                        ]);
                        break;
                    case 'Ubah Data Mahasiswa':
                         $request->validate(['data_nama_lengkap' => 'required', 'data_tmp_lahir' => 'required', 'data_tgl_lhr' => 'required']);
                        DetailTiketUbahDataMhs::create([
                            'tiket_id' => $tiket->id,
                            'data_nama_lengkap' => $request->data_nama_lengkap,
                            'data_tmp_lahir' => $request->data_tmp_lahir,
                            'data_tgl_lhr' => $request->data_tgl_lhr,
                        ]);
                        break;
                    case 'Request Publikasi Event':
                         $request->validate(['judul' => 'required', 'kategori' => 'required', 'konten' => 'required']);
                        DetailTiketReqPublikasi::create([
                            'tiket_id' => $tiket->id,
                            'judul' => $request->judul,
                            'kategori' => $request->kategori,
                            'konten' => $request->konten,
                            'gambar' => $request->gambar,
                        ]);
                        break;
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat tiket: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('mahasiswa.tiket.index')->with('success', 'Tiket layanan berhasil diajukan.');
    }

    public function show(Tiket $tiket)
    {
        if ($tiket->pemohon_id !== Auth::id()) { abort(403); }
        $tiket->load('layanan.unit', 'user', 'komentars.user', 'riwayatStatus.user');
        return view('mahasiswa.tiket.show', compact('tiket'));
    }
    
    public function storeComment(Request $request, Tiket $tiket)
    {
        if ($tiket->pemohon_id !== Auth::id()) { abort(403); }
        $request->validate(['komentar' => 'required|string']);
        KomentarTiket::create([
            'tiket_id' => $tiket->id, 
            'pengirim_id' => Auth::id(),
            'komentar' => $request->komentar
        ]);
        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}