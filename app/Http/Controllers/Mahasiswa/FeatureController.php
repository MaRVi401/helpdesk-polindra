<?php

namespace App\Http\Controllers\Mahasiswa;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Artikel;
use App\Models\Faq;
use App\Models\KategoriArtikel;


class FeatureController extends Controller
{
    // FAQ
    public function faq()
    {
        $data_faq = Faq::with(['layanan', 'user'])
            ->orderBy('created_at', 'asc')
            ->get();
        return view('content.apps.mahasiswa.feature.faq', compact('data_faq'), ['pageConfigs' => $this->pageConfigs]);
    }

    // ARTICLE
    public function article(Request $request)
    {
        $data_artikel = Artikel::with(['kategori', 'user'])
            ->where('status', 'post')
            ->orderBy('created_at', 'asc')
            ->get();

        $data_kategori = KategoriArtikel::orderBy('kategori')->get();

        return view(
            'content.apps.mahasiswa.feature.article',
            compact('data_artikel', 'data_kategori'),
            ['pageConfigs' => $this->pageConfigs]
        );
    }

    public function articleDetail($slug)
    {
        $artikel = Artikel::with(['kategori', 'user'])
            ->where('status', 'post')
            ->where('slug', $slug)
            ->firstOrFail();

        // Artikel lain (7 artikel terbaru, exclude artikel yang sedang dibuka)
        $artikel_lain = Artikel::with(['kategori', 'user'])
            ->where('status', 'post')
            ->where('id', '!=', $artikel->id)
            ->orderBy('created_at', 'desc')
            ->limit(7)
            ->get();

        return view(
            'content.apps.mahasiswa.feature.article-detail',
            compact('artikel', 'artikel_lain'),
            ['pageConfigs' => $this->pageConfigs]
        );
    }

    public function aboutUs()
    {
        return view('content.apps.mahasiswa.feature.about-us', ['pageConfigs' => $this->pageConfigs]);
    }
}