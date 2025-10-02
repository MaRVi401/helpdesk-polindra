<?php
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleLoginController;

Route::get('/', function () {
    return view('welcome');
});

// --- ROUTE FOR USERS WHO HAVE NOT LOGGED IN (GUEST) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('auth.login');

    // Login Users
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Google Authentication Route
    Route::get('auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);
});

// --- ROUTE FOR ALREADY LOGGED IN USERS (AUTH) ---
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Logout Users
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Super Admin
    Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');
    });
    // Mahasiswa
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', function () {
            return view('mahasiswa.dashboard');
        })->name('dashboard');
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
