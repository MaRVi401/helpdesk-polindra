<?php


use App\Http\Controllers\Admin\ArticleCategoryController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\MajorController;
use App\Http\Controllers\Admin\ManageUsers\StaffController;
use App\Http\Controllers\Admin\ManageUsers\StudentController;
use App\Http\Controllers\Admin\ManageArticleController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\StudyProgramController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UnitControllerOld;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\CompleteProfileController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\apps\FaqList;
use App\Http\Controllers\Pages\AuthPage;
use App\Http\Controllers\Pages\TestPage;
use App\Http\Controllers\Pages\LandingPage;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\Admin\ArtikelController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\KelolaFaqController;
use App\Http\Controllers\apps\EcommerceProductList;
use App\Http\Controllers\Mahasiswa\TiketController;
use App\Http\Controllers\Admin\AdminTiketController;
use App\Http\Controllers\Admin\ProgramStudiController;
use App\Http\Controllers\Admin\KategoriArtikelController;
use App\Http\Controllers\KepalaUnit\MonitoringTiketController;
use App\Http\Controllers\KepalaUnit\KelolaPicController;
use App\Http\Controllers\Mahasiswa\TiketController as MahasiswaTiketController;
use App\Http\Controllers\KepalaUnit\TiketController as KepalaUnitTiketController;
use App\Http\Controllers\AdminUnit\TiketController as AdminUnitTiketController;
use App\Http\Controllers\AdminUnit\LayananController as AdminUnitLayananController;


// FOR TESTING BLADE
Route::get('/test', [TestPage::class, 'home'])->name('home.page');

// --- ROUTE FOR USERS WHO HAVE NOT LOGGED IN (GUEST) ---
Route::middleware('guest')->group(function () {

    Route::get('/', [LandingPage::class, 'landingPage'])->name('landing.page');

    Route::get('/login', [AuthPage::class, 'authPage'])->name('auth.page');
    // Login Users
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Google Authentication Route
    Route::get('auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);


    Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

    // FORGOT PASSWORD
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])
        ->middleware('guest')
        ->name('password.request');

    // Send Reset Link Email
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
        ->middleware('guest')
        ->name('password.email');

    // Reset Password Form
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])
        ->middleware('guest')
        ->name('password.reset');

    // Submit New Password
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])
        ->middleware('guest')
        ->name('password.update');
});

// --- ROUTE FOR ALREADY LOGGED IN USERS (AUTH) ---
Route::middleware(['auth', 'complete-profile'])->group(function () {

    // DASHBOARD USERS
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // USER PROFILE
    Route::get('/user-profile', [UserProfileController::class, 'userProfile'])->name('user-profile.index');
    Route::get('/user-profile/setting-profile', [UserProfileController::class, 'setProfile'])->name('user-profile.set-profile');
    Route::put('/user-profile/update', [UserProfileController::class, 'userProfileUpdate'])->name('user-profile.update');
    Route::get('/user-profile/setting-security', [UserProfileController::class, 'setSecurity'])->name('user-profile.set-security');
    Route::post('/user-profile/update-password', [UserProfileController::class, 'userPasswordUpdate'])->name('user-profile.update-password');

    // COMPLETE PROFILE (STUDENT)
    Route::get('/complete-profile', [CompleteProfileController::class, 'completeProfile'])->name('complete.profile');
    Route::post('/save-profile', [CompleteProfileController::class, 'saveCompleteProfile'])->name('save.complete.profile');

    // USERS MANAGEMENT (SUPER ADMIN)
    Route::resource('student', StudentController::class)->middleware('role:super_admin');
    Route::resource('staff', StaffController::class)->middleware('role:super_admin');

    // MAJOR MANAGEMENT (SUPER ADMIN)
    Route::resource('major', MajorController::class)->middleware('role:super_admin');
    Route::resource('study-program', StudyProgramController::class)->middleware('role:super_admin');

    // UNIT MANAGEMENT (SUPER ADMIN)
    Route::resource('unit', UnitController::class)->middleware('role:super_admin');

    // FAQ MANAGEMENT (SUPER ADMIN)
    Route::resource('faq', FaqController::class)->middleware('role:super_admin');

    // SERVICE MANAGEMENT (SUPER ADMIN)
    Route::prefix('service')->name('service.')->group(function () {
        Route::get('/{slug}', [ServiceController::class, 'filterByUnit'])->name('unit');
        Route::get('/{slug}/{id}', [ServiceController::class, 'show'])->name('show');
        Route::get('/{slug}/{id}/edit', [ServiceController::class, 'edit'])->name('edit');
    });
    Route::resource('service', ServiceController::class)->only(['store', 'update', 'destroy']);

    // ARTICLE MANAGEMENT (SUPER ADMIN)
    Route::resource('article', ArticleController::class)->middleware('role:super_admin');
    Route::resource('article-category', ArticleCategoryController::class)->middleware('role:super_admin');

    // LOGOUT USERS
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Super Admin
    Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {



        // Kelola Pengguna - Mahasiswa
        // Route::get('mahasiswa/export/excel', [MahasiswaController::class, 'exportExcel'])->name('mahasiswa.export.excel');
        // Route::resource('mahasiswa', MahasiswaController::class);

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
        Route::get('unit/export/excel', [UnitControllerOld::class, 'exportExcel'])->name('unit.export.excel');
        Route::resource('unit', UnitControllerOld::class)->names('unit');

        // Kelola Artikel dan Kategori Artikel
        Route::get('artikel/export/excel', [ArtikelController::class, 'exportExcel'])->name('artikel.export.excel');
        Route::resource('artikel', ArtikelController::class)->names('artikel');
        Route::resource('kategori-artikel', KategoriArtikelController::class)->names('kategori-artikel');

        // Kelola Tiket Semua Unit
        Route::get('tiket/export/excel', [AdminTiketController::class, 'exportExcel'])->name('tiket.export.excel');
        Route::resource('tiket', AdminTiketController::class);

        // Kelola PIC Layanan
        Route::resource('layanan', LayananController::class);
    });


    // Mahasiswa
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', [MahasiswaTiketController::class, 'dashboard'])->name('dashboard');
        Route::get('/tiket', [MahasiswaTiketController::class, 'index'])->name('tiket.index');
        Route::get('/tiket/create', [MahasiswaTiketController::class, 'create'])->name('tiket.create');
        Route::post('/tiket', [MahasiswaTiketController::class, 'store'])->name('tiket.store');
        Route::get('/tiket/{id}', [MahasiswaTiketController::class, 'show'])->name('tiket.show');
        Route::post('tiket/{tiket}/komentar', [TiketController::class, 'storeKomentar'])->name('tiket.komentar.store');
        Route::get('/profil', [ProfileController::class, 'edit'])->name('profil.edit');
        Route::patch('/profil', [ProfileController::class, 'update'])->name('profil.update');
        Route::get('buat-tiket', [MahasiswaTiketController::class, 'showCreateForm'])->name('tiket.show-create-form');
        Route::resource('tiket', MahasiswaTiketController::class);
        Route::post('tiket/{id}/komentar', [MahasiswaTiketController::class, 'storeKomentar'])->name('tiket.komentar.store');
        Route::post('tiket/{id}/komentar', [MahasiswaTiketController::class, 'storeKomentar'])->name('tiket.storeKomentar');
        Route::patch('tiket/{id}/update-status', [MahasiswaTiketController::class, 'updateStatus'])->name('tiket.updateStatus');
    });

    // Kepala Unit
    Route::middleware(['role:kepala_unit'])->group(function () {
        Route::prefix('kepala-unit')->name('kepala-unit.')->group(function () {
            Route::get('/monitoring-tiket', [MonitoringTiketController::class, 'index'])->name('monitoring.index');
            Route::get('/monitoring-tiket/{tiket}', [MonitoringTiketController::class, 'show'])->name('monitoring.show');
            Route::put('/monitoring-tiket/{tiket}', [MonitoringTiketController::class, 'update'])->name('monitoring.update');
            Route::post('/monitoring-tiket/{tiket}/komentar', [MonitoringTiketController::class, 'storeKomentar'])->name('monitoring.komentar');
            Route::get('/kelola-pic', [KelolaPicController::class, 'index'])->name('pic.index');
            Route::get('/kelola-pic/{layanan}/edit', [KelolaPicController::class, 'edit'])->name('pic.edit');
            Route::put('/kelola-pic/{layanan}', [KelolaPicController::class, 'update'])->name('pic.update');
            Route::get('/monitoring-tiket/{tiket}/edit', [MonitoringTiketController::class, 'edit'])->name('monitoring.edit');
            Route::put('/monitoring-tiket/{tiket}', [MonitoringTiketController::class, 'update'])->name('monitoring.update');
            Route::post('/monitoring-tiket/{tiket}/komentar', [MonitoringTiketController::class, 'storeKomentar'])->name('monitoring.komentar');
            Route::put('/monitoring-tiket/{id}/update-timer', [MonitoringTiketController::class, 'updateTimer'])->name('monitoring.update-timer');
        });

    });
    // Admin Unit
    Route::middleware('role:admin_unit')->prefix('admin-unit')->name('admin_unit.')->group(function () {
        Route::get('/dashboard', [AdminUnitTiketController::class, 'index'])->name('dashboard');
        Route::get('/tiket', [AdminUnitTiketController::class, 'index'])->name('tiket.index');
        Route::get('/tiket/{id}', [AdminUnitTiketController::class, 'show'])->name('tiket.show');
        Route::put('/tiket/{id}', [AdminUnitTiketController::class, 'update'])->name('tiket.update');
        Route::post('/tiket/{id_tiket}/komentar', [AdminUnitTiketController::class, 'storeKomentar'])->name('tiket.storeKomentar');
        Route::resource('layanan', AdminUnitLayananController::class);
    });
});