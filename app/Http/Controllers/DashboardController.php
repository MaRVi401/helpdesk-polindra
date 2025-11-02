<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // $role = Auth::user()->role;
        $user = auth()->user();
        
        return view('content.pages.dashboard', compact('user'));
        
    }

    // public function super_admin()
    // {
    //     return view('content.pages.dashboard');
    // }

    // public function mahasiswa()
    // {
    //     $userId = Auth::id();
      
    //     $totalTiket = Tiket::where('id_user', $userId)->count();
    //     $tiketMenunggu = Tiket::where('id_user', $userId)->where('status', 'Menunggu')->count();
    //     $tiketDiproses = Tiket::where('id_user', $userId)->where('status', 'Diproses')->count();
    //     $tiketSelesai = Tiket::where('id_user', $userId)->where('status', 'Selesai')->count();

    //     return view('content.pages.dashboard', compact(
    //         'totalTiket',
    //         'tiketMenunggu',
    //         'tiketDiproses',
    //         'tiketSelesai'
    //     ));
    // }

    // public function kepala_unit()
    // {
    //     return view('content.pages.dashboard');
    // }

    // public function admin_unit()
    // {
    //     return view('content.pages.dashboard');
    // }
}