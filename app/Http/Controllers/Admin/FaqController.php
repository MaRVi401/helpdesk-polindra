<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        return view('content.apps.app-faq-list');
    }

    public function getList(Request $request)
    {
        try {
            $faqs = Faq::with(['user:id,name', 'layanan:id,nama'])
                ->select('id', 'user_id', 'layanan_id', 'judul', 'deskripsi', 'status')
                ->get()
                ->map(function($faq) {
                    return [
                        'id' => $faq->id,
                        'user_id' => $faq->user_id,
                        'layanan_id' => $faq->layanan_id,
                        'judul' => $faq->judul,
                        'deskripsi' => $faq->deskripsi,
                        'status' => $faq->status, // 'Post' atau status lainnya
                        'user' => $faq->user ? ['name' => $faq->user->name] : null,
                        'layanan' => $faq->layanan ? ['nama' => $faq->layanan->nama] : null
                    ];
                });

            return response()->json([
                'data' => $faqs->values()
            ]);

        } catch (\Exception $e) {
            \Log::error('FAQ Error: ' . $e->getMessage());
            
            return response()->json([
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $faq = Faq::findOrFail($id);
            $faq->delete();

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