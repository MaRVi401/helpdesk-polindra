<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KomentarTiket;
use App\Models\Layanan;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\DetailTiketSuratKetAktif;
use App\Models\DetailTiketResetAkun;
use App\Models\DetailTiketUbahDataMhs;
use App\Models\DetailTiketReqPublikasi;
use Illuminate\Support\Facades\DB;

class TiketController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $tikets = Tiket::where('pemohon_id', $userId)
            ->with(['layanan.unit', 'riwayatStatus' ])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('mahasiswa.tiket.index', compact('tikets'));
    }

    public function create()
    {
        return redirect()->route('mahasiswa.tiket.show-create-form');
    }

    public function showCreateForm(Request $request)
    {
        $layanans = Layanan::with('unit')->get();
        $layananTerpilih = null;

        if ($request->has('layanan_id')) {
            $layananTerpilih = Layanan::find($request->layanan_id);
        }

        return view('mahasiswa.tiket.create', compact('layanans', 'layananTerpilih'));
    }

    public function store(Request $request)
    {
        $baseRules = [
            'layanan_id' => 'required|exists:layanan,id',
            'deskripsi' => 'required|string|min:10',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120',
        ];

        $layanan = Layanan::find($request->layanan_id);
        if (!$layanan) {
            return redirect()->route('mahasiswa.tiket.show-create-form')
                ->withErrors(['layanan_id' => 'Layanan yang dipilih tidak valid.']);
        }

        $detailRules = [];
        switch ($layanan->nama) {
            case 'Surat Keterangan Aktif Kuliah':
                $detailRules = [
                    'keperluan' => 'required|string|max:255',
                    'tahun_ajaran' => 'required|numeric|digits:4',
                    'semester' => 'required|integer|min:1|max:14',
                ];
                break;
            case 'Reset Akun E-Learning & Siakad':
            case 'Permintaan Reset Akun E-Mail':
                $detailRules = ['aplikasi' => 'required|in:gmail,office,sevima'];
                break;
            case 'Ubah Data Mahasiswa':
                $detailRules = [
                    'data_nama_lengkap' => 'required|string|max:255',
                    'data_tmp_lahir' => 'required|string|max:100',
                    'data_tgl_lhr' => 'required|date_format:Y-m-d',
                ];
                break;
            case 'Request Publikasi Event':
                $detailRules = [
                    'judul_publikasi' => 'required|string|max:255',
                    'kategori_publikasi' => 'required|string|max:100',
                    'konten' => 'required|string',
                    'gambar_publikasi' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
                ];
                break;
        }

        $allRules = array_merge($baseRules, $detailRules);
        $request->validate($allRules);

        try {
            DB::beginTransaction();

            $tiket = new Tiket();
            $tiket->pemohon_id = Auth::id();
            $tiket->layanan_id = $request->layanan_id;
            $tiket->deskripsi = $request->deskripsi;
            $tiket->no_tiket = time();

            if ($request->hasFile('lampiran')) {
                $path = $request->file('lampiran')->store('lampiran_tiket', 'public');
                $tiket->lampiran = $path;
            }
            
            $tiket->save(); 

            $detailData = $request->all();
            $detailData['tiket_id'] = $tiket->id; 

            switch ($layanan->nama) {
                case 'Surat Keterangan Aktif Kuliah':
                    DetailTiketSuratKetAktif::create($detailData);
                    break;
                case 'Reset Akun E-Learning & Siakad':
                case 'Permintaan Reset Akun E-Mail':
                    DetailTiketResetAkun::create($detailData);
                    break;
                case 'Ubah Data Mahasiswa':
                    DetailTiketUbahDataMhs::create($detailData);
                    break;
                case 'Request Publikasi Event':
                    $detailData['judul'] = $detailData['judul_publikasi'];
                    $detailData['kategori'] = $detailData['kategori_publikasi'];
                    if ($request->hasFile('gambar_publikasi')) {
                        $path = $request->file('gambar_publikasi')->store('lampiran_publikasi', 'public');
                        $detailData['gambar'] = $path;
                    }
                    DetailTiketReqPublikasi::create($detailData);
                    break;
            }

            DB::commit();
            return redirect()->route('mahasiswa.tiket.show', $tiket->id)->with('success', 'Tiket berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan tiket: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $userId = Auth::id();
        $tiket = Tiket::where('id', $id)
            ->where('pemohon_id', $userId)
            ->with('layanan.unit', 'komentar.pengirim.mahasiswa', 'komentar.pengirim.staff')
            ->firstOrFail();
            
        $detail = null;
        switch ($tiket->layanan->nama) {
            case 'Surat Keterangan Aktif Kuliah':
                $detail = DetailTiketSuratKetAktif::where('tiket_id', $tiket->id)->first();
                break;
            case 'Reset Akun E-Learning & Siakad':
            case 'Permintaan Reset Akun E-Mail':
                $detail = DetailTiketResetAkun::where('tiket_id', $tiket->id)->first();
                break;
            case 'Ubah Data Mahasiswa':
                $detail = DetailTiketUbahDataMhs::where('tiket_id', $tiket->id)->first();
                break;
            case 'Request Publikasi Event':
                $detail = DetailTiketReqPublikasi::where('tiket_id', $tiket->id)->first();
                break;
        }

        return view('mahasiswa.tiket.show', compact('tiket', 'detail'));
    }

    public function edit(string $id)
    {
        return redirect()->route('mahasiswa.tiket.show', $id);
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('mahasiswa.tiket.show', $id);
    }

    public function destroy(string $id)
    {
        $userId = Auth::id();
        $tiket = Tiket::where('id', $id)
            ->where('pemohon_id', $userId)
            ->firstOrFail();
        try {
            DB::beginTransaction();
            KomentarTiket::where('tiket_id', $tiket->id)->delete();
            switch ($tiket->layanan->nama) {
                case 'Surat Keterangan Aktif Kuliah':
                    DetailTiketSuratKetAktif::where('tiket_id', $tiket->id)->delete();
                    break;
                case 'Reset Akun E-Learning & Siakad':
                case 'Permintaan Reset Akun E-Mail':
                    DetailTiketResetAkun::where('tiket_id', $tiket->id)->delete();
                    break;
                case 'Ubah Data Mahasiswa':
                    DetailTiketUbahDataMhs::where('tiket_id', $tiket->id)->delete();
                    break;
                case 'Request Publikasi Event':
                    // Hapus gambar jika ada
                    $detailPub = DetailTiketReqPublikasi::where('tiket_id', $tiket->id)->first();
                    if ($detailPub && $detailPub->gambar) {
                        Storage::disk('public')->delete($detailPub->gambar);
                    }
                    $detailPub->delete();
                    break;
            }
            if ($tiket->lampiran) {
                Storage::disk('public')->delete($tiket->lampiran);
            }
            $tiket->delete();

            DB::commit();

            return redirect()->route('mahasiswa.tiket.index')->with('success', 'Tiket berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus tiket: ' . $e->getMessage());
        }
    }

    public function storeKomentar(Request $request, $tiketId)
    {
        $request->validate([
            'komentar' => 'required|string', 
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:5120',
        ]);

        $userId = Auth::id();
        $tiket = Tiket::where('id', $tiketId)
            ->where('pemohon_id', $userId) 
            ->firstOrFail();

        if ($tiket->jawaban_id) { 
            return back()->with('error', 'Tiket ini telah ditutup.');
        }

        $komentar = new KomentarTiket();
        $komentar->tiket_id = $tiket->id;
        $komentar->pengirim_id = Auth::id(); 
        $komentar->komentar = $request->komentar; 

        if ($request->hasFile('lampiran')) {
            $path = $request->file('lampiran')->store('lampiran_komentar', 'public');
            $komentar->lampiran = $path;
        }
        $komentar->save();

        return redirect()->route('mahasiswa.tiket.show', $tiket->id)->with('success', 'Komentar berhasil ditambahkan.');
    }
}