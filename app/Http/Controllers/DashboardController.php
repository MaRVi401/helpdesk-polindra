<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
                return redirect()->route('kepala_unit.dashboard');
            case 'admin_unit':
                return redirect()->route('admin_unit.dashboard');
            default:
                return redirect('/');
        }
    }
}
