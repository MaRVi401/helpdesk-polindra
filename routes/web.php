<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GoogleLoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman utama
Route::get('/', function () {
    return view('welcome');
});

// --- RUTE UNTUK PENGGUNA YANG BELUM LOGIN (GUEST) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');

    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    // Rute Otentikasi Google
    Route::get('auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);
});


// --- RUTE UNTUK PENGGUNA YANG SUDAH LOGIN (AUTH) ---
Route::middleware('auth')->group(function () {

    // 1. Rute Dispatcher Dashboard
    Route::get('/dashboard', function () {
        $role = Auth::user()->role;

        if ($role == 'super_admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role == 'mahasiswa') {
            return redirect()->route('mahasiswa.dashboard');
        } elseif ($role == 'kepala_unit') {
            return redirect()->route('kepala_unit.dashboard');
        } elseif ($role == 'admin_unit') {
            return redirect()->route('admin_unit.dashboard');
        } else {
            return redirect('/');
        }
    })->name('dashboard');

    // 2. Rute Logout
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');


    // 3. Grup Rute untuk Admin & Super Admin
    Route::middleware('role:super_admin,admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
    });

    // 4. Grup Rute untuk Mahasiswa
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () { // Diperbaiki: Middleware hanya untuk role 'mahasiswa'
        Route::get('/dashboard', function () {
            return view('mahasiswa.dashboard');
        })->name('dashboard');
    });

    // 5. Grup Rute untuk Kepala Unit
    Route::middleware('role:kepala_unit')->prefix('kepala-unit')->name('kepala_unit.')->group(function () {
        Route::get('/dashboard', function () {
            return view('kepala_unit.dashboard');
        })->name('dashboard');
    });

    // 6. Grup Rute untuk Admin Unit
    Route::middleware('role:admin_unit')->prefix('admin-unit')->name('admin_unit.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin_unit.dashboard');
        })->name('dashboard');
    });
});
