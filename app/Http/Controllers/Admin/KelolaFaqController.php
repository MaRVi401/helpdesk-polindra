<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FaqExport;

class KelolaFaqController extends Controller
{
    /**
     * Menampilkan daftar FAQ dengan paginasi dan pencarian.
     */
    public function index(Request $request)
    {
        $searchQuery = $request->input('q');
        $perPage = $request->input('per_page', 10);

        $query = Faq::with(['user', 'layanan'])->orderBy('id', 'asc');

        if ($searchQuery) {
            $query->where('judul', 'like', '%' . $searchQuery . '%')
                ->orWhere('deskripsi', 'like', '%' . $searchQuery . '%');
        }

        $faqs = $query->paginate($perPage)->withQueryString();

        return view('admin.kelolafaq.index', compact('faqs', 'searchQuery', 'perPage'));
    }

    /**
     * Menampilkan form untuk membuat FAQ baru.
     */
    public function create()
    {
        $layanans = Layanan::all();
        return view('admin.kelolafaq.create', compact('layanans'));
    }

    /**
     * Menyimpan FAQ baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'layanan_id' => 'required|exists:layanan,id',
            'deskripsi' => 'required|string',
            'status' => 'required|in:Draft,Post',
        ]);

        Faq::create([
            'user_id' => Auth::id(),
            'layanan_id' => $request->layanan_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.kelolafaq.index')->with('success', 'FAQ berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit FAQ.
     */
    public function edit(Faq $kelolafaq)
    {
        $layanans = Layanan::all();
        return view('admin.kelolafaq.edit', [
            'faq' => $kelolafaq,
            'layanans' => $layanans
        ]);
    }

    /**
     * Memperbarui data FAQ di database.
     */
    public function update(Request $request, Faq $kelolafaq)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'layanan_id' => 'required|exists:layanan,id',
            'deskripsi' => 'required|string',
            'status' => 'required|in:Draft,Post',
        ]);

        $kelolafaq->update($request->all());

        return redirect()->route('admin.kelolafaq.index')->with('success', 'FAQ berhasil diperbarui.');
    }

    /**
     * Menghapus data FAQ dari database.
     */
    public function destroy(Faq $kelolafaq)
    {
        try {
            $kelolafaq->delete();
            return redirect()->route('admin.kelolafaq.index')->with('success', 'FAQ berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.kelolafaq.index')->with('error', 'Gagal menghapus FAQ. Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Mengekspor data FAQ yang dipilih ke Excel.
     */
    public function exportExcel(Request $request)
    {
        $selectedFaqs = $request->input('selected_faqs');

        if (empty($selectedFaqs)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih untuk diekspor.');
        }

        return Excel::download(new FaqExport($selectedFaqs), 'data-faq.xlsx');
    }
}
