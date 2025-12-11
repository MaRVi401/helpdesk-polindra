<?php

use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Admin\ArticleCategoryController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\MajorController;
use App\Http\Controllers\Admin\ManageUsers\StaffController;
use App\Http\Controllers\Admin\ManageUsers\StudentController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\StudyProgramController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UnitControllerOld;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\CompleteProfileController;
use App\Http\Controllers\Mahasiswa\ServiceTicketController;
use App\Http\Controllers\Pages\LandingController;
use App\Http\Controllers\Profile\UserProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pages\TestPage;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Mahasiswa\FaqController as ServiceDeskFAQ;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Pages\DashboardController;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\Admin\ArtikelController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\KelolaFaqController;
use App\Http\Controllers\Mahasiswa\TiketController;
use App\Http\Controllers\Admin\AdminTiketController;
use App\Http\Controllers\Admin\ProgramStudiController;
use App\Http\Controllers\Admin\KategoriArtikelController;
use App\Http\Controllers\KepalaUnit\MonitoringTiketController;
use App\Http\Controllers\KepalaUnit\KelolaPicController;
use App\Http\Controllers\AdminUnit\ServiceTicketController as AdminUnitServiceTicketController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\PositionControllerOld;
use App\Http\Controllers\KepalaUnit\DashboardController as KepalaUnitDashboardController;

// FOR TESTING BLADE
Route::get('/test', [TestPage::class, 'index'])->name('page.index');

// --- ROUTE FOR USERS WHO HAVE NOT LOGGED IN (GUEST) ---
Route::middleware('guest')->group(function () {
    Route::get('/', [LandingController::class, 'landingPage'])->name('landing.page');
    Route::get('/login', [AuthController::class, 'authPage'])->name('auth.page');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Google Authentication Route
    Route::get('auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);

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

    // LOGOUT USERS
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // USER PROFILE
    Route::get('/user-profile', [UserProfileController::class, 'userProfile'])->name('user-profile.index');
    Route::get('/user-profile/setting-profile', [UserProfileController::class, 'setProfile'])->name('user-profile.set-profile');
    Route::put('/user-profile/update', [UserProfileController::class, 'userProfileUpdate'])->name('user-profile.update');
    Route::get('/user-profile/setting-security', [UserProfileController::class, 'setSecurity'])->name('user-profile.set-security');
    Route::post('/user-profile/update-password', [UserProfileController::class, 'userPasswordUpdate'])->name('user-profile.update-password');

    // COMPLETE PROFILE (STUDENT)
    Route::get('/complete-profile', [CompleteProfileController::class, 'completeProfile'])->name('complete.profile');
    Route::post('/save-profile', [CompleteProfileController::class, 'saveCompleteProfile'])->name('save.complete.profile');

    Route::middleware('role:super_admin')->group(function () {
        // SERVICE MANAGEMENT (SUPER ADMIN)
        Route::prefix('service')->name('service.')->group(function () {
            Route::get('/{unitSlug}', [ServiceController::class, 'filterByUnit'])->name('unit');
            Route::get('/{unitSlug}/{layananSlug}', [ServiceController::class, 'show'])->name('show');
            Route::get('/{unitSlug}/{layananSlug}/edit', [ServiceController::class, 'edit'])->name('edit');
        });
        Route::resource('service', ServiceController::class)->only(['store', 'update', 'destroy']);

        // USERS MANAGEMENT (SUPER ADMIN)
        Route::resource('student', StudentController::class);
        Route::resource('staff', StaffController::class);

        // MAJOR MANAGEMENT (SUPER ADMIN)
        Route::resource('major', MajorController::class);
        Route::resource('study-program', StudyProgramController::class);

        // UNIT MANAGEMENT (SUPER ADMIN)
        Route::resource('unit', UnitController::class);

        // FAQ MANAGEMENT (SUPER ADMIN)
        Route::resource('faq', FaqController::class);

        // ARTICLE MANAGEMENT (SUPER ADMIN)
        Route::resource('article', ArticleController::class);
        Route::resource('article-category', ArticleCategoryController::class);

        // POSITION MANAGEMENT (SUPER ADMIN)
        // Route::resource('position', PositionControllerOld::class)->names('position');
        Route::resource('position', PositionController::class)->names('position');
        
        // TICKET MANAGEMENT (SUPER ADMIN) 
        Route::resource('tiket', AdminTicketController::class)->names('ticket');
        
    });

    // SERVICE TICKET (MAHASISWA)
    Route::middleware('role:mahasiswa')->group(function () {
        Route::resource('service-ticket', ServiceTicketController::class);
        Route::post('service-ticket/{id}/comment', [ServiceTicketController::class, 'serviceTicketComment'])->name('service.ticket.comment');
        Route::patch('service-ticket/{id}/status-confirm', [ServiceTicketController::class, 'statusConfirm'])->name('service.ticket.statusConfirm');
        Route::put('/service-ticket/{id}/update-timer', [ServiceController::class, 'updateTimer'])->name('service-ticket.updateTimer');
        Route::get('/servicedesk-faq', [ServiceDeskFAQ::class, 'index'])->name('servicedesk.faq.index');
    });


    // // Super Admin
    // Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {
    //     ;
    //     // Kelola Pengguna - Staff
    //     Route::get('staff/export/excel', [StaffController::class, 'exportExcel'])->name('staff.export.excel');
    //     Route::resource('staff', StaffController::class);

    //     // Kelola FAQ
    //     Route::get('kelolafaq/export/excel', [KelolaFaqController::class, 'exportExcel'])->name('kelolafaq.export.excel');
    //     Route::resource('kelolafaq', KelolaFaqController::class);

    //     // Kelola Jurusan
    //     Route::get('jurusan/export/excel', [JurusanController::class, 'exportExcel'])->name('jurusan.export.excel');
    //     Route::resource('jurusan', JurusanController::class)->names('jurusan');

    //     // Kelola Program Studi
    //     Route::get('program-studi/export/excel', [ProgramStudiController::class, 'exportExcel'])->name('program-studi.export.excel');
    //     Route::resource('program-studi', ProgramStudiController::class)->except(['index', 'show'])->names('program-studi');

    //     // Master Route Jurusan & Prodi
    //     Route::get('jurusan/{jurusan}/program-studi', [ProgramStudiController::class, 'index'])->name('jurusan.program-studi.index');

    //     // Kelola Unit
    //     Route::get('unit/export/excel', [UnitControllerOld::class, 'exportExcel'])->name('unit.export.excel');
    //     Route::resource('unit', UnitControllerOld::class)->names('unit');

    //     // Kelola Artikel dan Kategori Artikel
    //     Route::get('artikel/export/excel', [ArtikelController::class, 'exportExcel'])->name('artikel.export.excel');
    //     Route::resource('artikel', ArtikelController::class)->names('artikel');
    //     Route::resource('kategori-artikel', KategoriArtikelController::class)->names('kategori-artikel');

    //     // Kelola Tiket Semua Unit
    //     Route::get('tiket/export/excel', [AdminTiketController::class, 'exportExcel'])->name('tiket.export.excel');
    //     Route::resource('tiket', AdminTiketController::class);

    //     // Kelola PIC Layanan
    //     Route::resource('layanan', LayananController::class);
    // });


    // Kepala Unit
    Route::middleware(['role:kepala_unit'])->group(function () {
        Route::prefix('kepala-unit')->name('kepala-unit.')->group(function () {
            Route::get('/dashboard', [KepalaUnitDashboardController::class, 'index'])->name('dashboard');
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
    Route::middleware(['auth', 'role:admin_unit'])->prefix('admin-unit')->name('admin_unit.')->group(function () {
        Route::get('/service-ticket', [AdminUnitServiceTicketController::class, 'index'])->name('ticket.index');
        Route::get('/service-ticket/{id}', [AdminUnitServiceTicketController::class, 'show'])->name('ticket.show');
        Route::put('/service-ticket/{id}', [AdminUnitServiceTicketController::class, 'update'])->name('ticket.update');
        Route::post('/service-ticket/{id}/comment', [AdminUnitServiceTicketController::class, 'storeKomentar'])->name('ticket.comment');
        Route::put('/service-ticket/{id}/update-timer', [AdminUnitServiceTicketController::class, 'updateTimer'])->name('ticket.updateTimer');
    });
});