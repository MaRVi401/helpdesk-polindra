<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\GoogleLoginController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('auth/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);

use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/login', function () {
    return view('login');
})->name('login')->middleware('guest');
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard')->middleware('auth');
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    return redirect('/login');
})->name('logout');

Route::get('/admin', function () {
    return 'Ini adalah halaman khusus Admin dan Super Admin.';
})->middleware(['auth', 'role:admin,super_admin']);
