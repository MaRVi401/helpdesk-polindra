<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
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
        $tikets = Tiket::with('layanan')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('mahasiswa.tiket.index', compact('tikets'));
    }

    public function create()
    {
        $layanans = Layanan::where('status', true)->orderBy('nama')->get();
        return view('mahasiswa.tiket.create', compact('layanans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'layanan_id' => 'required|exists:layanan,id',
            'deskripsi' => 'required|string|min:10',
        ]);

        DB::beginTransaction();
        try {
            $tiket = Tiket::create([
                'user_id' => Auth::id(),
                'layanan_id' => $request->layanan_id,
                'no_tiket' => 'TICKET-' . time() . '-' . Auth::id(),
                'deskripsi' => $request->deskripsi,
                'status' => 'Diajukan',
            ]);

            RiwayatStatusTiket::create([
                'tiket_id' => $tiket->id,
                'user_id' => Auth::id(),
                'status' => 'Diajukan',
                'komentar' => 'Tiket berhasil dibuat oleh pemohon.',
            ]);

            DB::commit();

            return redirect()->route('mahasiswa.tiket.index')->with('success', 'Tiket layanan berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat tiket.')->withInput();
        }
    }

    public function show(Tiket $tiket)
    {
        if ($tiket->user_id !== Auth::id()) {
            abort(403);
        }

        $tiket->load(['layanan.unit', 'riwayatStatus.user', 'komentar.user']);
        return view('mahasiswa.tiket.show', compact('tiket'));
    }

    public function storeComment(Request $request, Tiket $tiket)
    {
        if ($tiket->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate(['komentar' => 'required|string|min:5']);

        KomentarTiket::create([
            'tiket_id' => $tiket->id,
            'user_id' => Auth::id(),
            'komentar' => $request->komentar,
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}