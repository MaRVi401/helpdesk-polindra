<?php


use App\Http\Controllers\Pages\LandingPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\apps\FaqList;
use App\Http\Controllers\Pages\AuthPage;
use App\Http\Controllers\Pages\TestPage;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\Admin\ArtikelController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\KelolaFaqController;
use App\Http\Controllers\apps\EcommerceProductList;
use App\Http\Controllers\Mahasiswa\TiketController;
use App\Http\Controllers\Admin\ProgramStudiController;
use App\Http\Controllers\Admin\KategoriArtikelController;
use App\Http\Controllers\Admin\KelolaPengguna\StaffController;
use App\Http\Controllers\Admin\KelolaPengguna\MahasiswaController;



// FOR TESTING BLADE
Route::get('/test', [TestPage::class, 'home'])->name('home.page');

Route::get('/app/ecommerce/product/list', [EcommerceProductList::class, 'index'])->name('app-ecommerce-product-list');
Route::get('/app/faq/all/list', [FaqList::class, 'index'])->name('app-faq-list');





// // Route untuk admin (harus login sebagai super_admin)
// Route::middleware(['auth', 'role:super_admin'])->prefix('/faq')->group(function () {
//     Route::get('/list', [FaqController::class, 'index'])->name('faq.list');
//     Route::get('/add', [FaqController::class, 'create'])->name('add');
//     Route::post('/add', [FaqController::class, 'store'])->name('add.faq');
//     Route::get('/view/{id}', [FaqController::class, 'view'])->name('view');
//     Route::get('/edit/{id}', [FaqController::class, 'edit'])->name('edit');
//     Route::delete('/delete/{id}', [FaqController::class, 'delete'])->name('delete');
// });

// --- ROUTE FOR USERS WHO HAVE NOT LOGGED IN (GUEST) ---
Route::middleware('guest')->group(function () {

    Route::get('/', [LandingPage::class, 'landingPage'])->name('landing.page');

    Route::get('/login', [AuthPage::class, 'authPage'])->name('auth.page');
    // Login Users
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Google Authentication Route
    Route::get('auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);


    // Route::get('/faq/get-list', [FaqController::class, 'getList'])->name('getList');
});

// --- ROUTE FOR ALREADY LOGGED IN USERS (AUTH) ---
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('content.pages.dashboard');
    })->name('dashboard');

    Route::resource('faq', FaqController::class)->middleware('role:super_admin');

    Route::get('/lengkapi-profil', [ProfileController::class, 'showCompletionForm'])->name('profile.completion.form');

    // Route untuk menyimpan data dari form
    Route::post('/lengkapi-profil', [ProfileController::class, 'saveCompletionForm'])->name('profile.completion.save');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    // Logout Users
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');




    // Super Admin
    Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {


        // Route::get('/add', [FaqController::class, 'create'])->name('add');
        // Route::post('/add', [FaqController::class, 'store'])->name('add.faq');
        // Route::get('/view/{id}', [FaqController::class, 'view'])->name('view');
        // Route::get('/edit/{id}', [FaqController::class, 'edit'])->name('edit');
        // Route::delete('/delete/{id}', [FaqController::class, 'delete'])->name('delete');


        // Kelola Pengguna - Mahasiswa
        Route::get('mahasiswa/export/excel', [MahasiswaController::class, 'exportExcel'])->name('mahasiswa.export.excel');
        Route::resource('mahasiswa', MahasiswaController::class);

        // Kelola Pengguna - Staff
        Route::get('staff/export/excel', [StaffController::class, 'exportExcel'])->name('staff.export.excel');
        Route::resource('staff', StaffController::class);

        // Kelola FAQ
        Route::get('kelolafaq/export/excel', [KelolaFaqController::class, 'exportExcel'])->name('kelolafaq.export.excel');
        Route::resource('kelolafaq', KelolaFaqController::class);

        // Kelola Jurusan
        Route::get('jurusan/export/excel', [JurusanController::class, 'exportExcel'])->name('jurusan.export.excel');
        Route::resource('jurusan', JurusanController::class)->names('jurusan');

        // Kelola Program Studi
        Route::get('program-studi/export/excel', [ProgramStudiController::class, 'exportExcel'])->name('program-studi.export.excel');
        Route::resource('program-studi', ProgramStudiController::class)->except(['index', 'show'])->names('program-studi');

        // Master Route Jurusan & Prodi
        Route::get('jurusan/{jurusan}/program-studi', [ProgramStudiController::class, 'index'])->name('jurusan.program-studi.index');

        // Kelola Unit
        Route::get('unit/export/excel', [UnitController::class, 'exportExcel'])->name('unit.export.excel');
        Route::resource('unit', UnitController::class)->names('unit');

        // Kelola Artikel dan Kategori Artikel
        Route::get('artikel/export/excel', [ArtikelController::class, 'exportExcel'])->name('artikel.export.excel');
        Route::resource('artikel', ArtikelController::class)->names('artikel');
        Route::resource('kategori-artikel', KategoriArtikelController::class)->names('kategori-artikel');
    });
    // Mahasiswa
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', function () {
            return view('content.pages.dashboard');
        })->name('dashboard');

        // Rute untuk fungsionalitas tiket mahasiswa
        Route::get('/tiket', [TiketController::class, 'index'])->name('tiket.index');
        Route::get('/tiket/create', [TiketController::class, 'create'])->name('tiket.create');
        Route::post('/tiket', [TiketController::class, 'store'])->name('tiket.store');
        Route::get('/tiket/{tiket}', [TiketController::class, 'show'])->name('tiket.show');
        Route::post('tiket/{tiket}/komentar', [TiketController::class, 'storeKomentar'])->name('tiket.storeKomentar');
    });
    // Kepala Unit
    Route::middleware('role:kepala_unit')->prefix('kepala-unit')->name('kepala_unit.')->group(function () {
        Route::get('/dashboard', function () {
            return view('content.pages.dashboard');
        })->name('dashboard');
    });
    // Admin Unit
    Route::middleware('role:admin_unit')->prefix('admin-unit')->name('admin_unit.')->group(function () {
        Route::get('/dashboard', function () {
            return view('content.pages.dashboard');
        })->name('dashboard');
    });
});