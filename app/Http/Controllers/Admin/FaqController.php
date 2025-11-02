<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Layanan;
use Auth;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    
    {
        $data_faq = Faq::with(['layanan', 'user'])
        ->orderBy('created_at', 'asc')
        ->get();
        return view('content.apps.admin.faq.list', compact('data_faq'));
    }

    public function create()
    {
        $data_layanan = Layanan::all();
        return view('content.apps.admin.faq.add', compact('data_layanan'));
    }

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

        return redirect()->route('faq.index')->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function show($id)
    {
        $data_faq = Faq::with(['layanan', 'user'])->findOrFail($id);
        return view('content.apps.admin.faq.show', compact('data_faq'));
    }
    

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'layanan_id' => 'required|exists:layanan,id',
            'deskripsi' => 'required|string',
            'status' => 'required|in:Draft,Post',
        ]);

        $data_faq = Faq::findOrFail($id);
        $data_faq->update([
            'layanan_id' => $request->layanan_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'status' => $request->status,
        ]);

        return redirect()->route('faq.index')->with('success', 'FAQ berhasil diupdate.');
    }

    public function destroy($id)
    {
        try {
            $data_faq = Faq::findOrFail($id);
            $data_faq->delete();

            return response()->json([
                'success' => true,
                'message' => 'FAQ berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus FAQ: ' . $e->getMessage()
            ], 500);
        }
    }
}