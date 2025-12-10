<?php

namespace App\Http\Controllers\Mahasiswa;
use App\Http\Controllers\Controller;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index()
    {
        $data_faq = Faq::with(['layanan', 'user'])
            ->orderBy('created_at', 'asc')
            ->get();
        return view('content.apps.mahasiswa.faq', compact('data_faq'),['pageConfigs' => $this->pageConfigs]);
    }
}