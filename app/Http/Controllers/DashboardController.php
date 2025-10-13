<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::user()->role;

        switch ($role) {
            case 'super_admin':
                return redirect()->route('admin.dashboard');
            case 'mahasiswa':
                return redirect()->route('mahasiswa.dashboard');
            case 'kepala_unit':
                return redirect()->route('kepala-unit.dashboard');
            case 'admin_unit':
                return redirect()->route('admin-unit.dashboard');
            default:
                return redirect('/');
        }
    }

    public function super_admin()
    {
        return view('admin.dashboard');
    }

    public function mahasiswa()
    {
        $userId = Auth::id();
      
        $totalTiket = Tiket::where('id_user', $userId)->count();
        $tiketMenunggu = Tiket::where('id_user', $userId)->where('status', 'Menunggu')->count();
        $tiketDiproses = Tiket::where('id_user', $userId)->where('status', 'Diproses')->count();
        $tiketSelesai = Tiket::where('id_user', $userId)->where('status', 'Selesai')->count();

        return view('mahasiswa.dashboard', compact(
            'totalTiket',
            'tiketMenunggu',
            'tiketDiproses',
            'tiketSelesai'
        ));
    }

    public function kepala_unit()
    {
        return view('kepala_unit.dashboard');
    }

    public function admin_unit()
    {
        return view('admin_unit.dashboard');
    }
}