<?php

use App\Http\Controllers\Pages\AuthPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\Admin\KelolaPengguna\MahasiswaController;
use App\Http\Controllers\Admin\KelolaPengguna\StaffController;
use App\Http\Controllers\Admin\KelolaFaqController;
use App\Http\Controllers\Mahasiswa\TiketController;



// --- ROUTE FOR USERS WHO HAVE NOT LOGGED IN (GUEST) ---
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthPage::class, 'login'])->name('login.page');
        
    // Login Users
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Google Authentication Route
    Route::get('auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);
});

// --- ROUTE FOR ALREADY LOGGED IN USERS (AUTH) ---
Route::middleware('auth')->group(function () {

    Route::get('/lengkapi-profil', [ProfileController::class, 'showCompletionForm'])->name('profile.completion.form');

    // Route untuk menyimpan data dari form
    Route::post('/lengkapi-profil', [ProfileController::class, 'saveCompletionForm'])->name('profile.completion.save');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Logout Users
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Super Admin
    Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Kelola Pengguna - Mahasiswa
        Route::get('mahasiswa/export/excel', [MahasiswaController::class, 'exportExcel'])->name('mahasiswa.export.excel');
        Route::resource('mahasiswa', MahasiswaController::class);

        // Kelola Pengguna - Staff
        Route::get('staff/export/excel', [StaffController::class, 'exportExcel'])->name('staff.export.excel');
        Route::resource('staff', StaffController::class);
        
        // Kelola FAQ
        Route::get('kelolafaq/export', [KelolaFaqController::class, 'exportExcel'])->name('kelolafaq.export.excel');
        Route::resource('kelolafaq', KelolaFaqController::class);
        

    });
    // Mahasiswa
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', function () {
            return view('mahasiswa.dashboard');
        })->name('dashboard');
        
        // Rute untuk fungsionalitas tiket mahasiswa
        Route::get('/tiket', [TiketController::class, 'index'])->name('tiket.index');
        Route::get('/tiket/create', [TiketController::class, 'create'])->name('tiket.create');
        Route::post('/tiket', [TiketController::class, 'store'])->name('tiket.store');
        Route::get('/tiket/{tiket}', [TiketController::class, 'show'])->name('tiket.show');
        Route::post('/tiket/{tiket}/komentar', [TiketController::class, 'storeComment'])->name('tiket.komentar.store');
    });
    // Kepala Unit
    Route::middleware('role:kepala_unit')->prefix('kepala-unit')->name('kepala_unit.')->group(function () {
        Route::get('/dashboard', function () {
            return view('kepala_unit.dashboard');
        })->name('dashboard');
    });
    // Admin Unit
    Route::middleware('role:admin_unit')->prefix('admin-unit')->name('admin_unit.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin_unit.dashboard');
        })->name('dashboard');
    });
});