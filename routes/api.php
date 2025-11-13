<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MahasiswaTicketController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Group route dengan API Key security manual
Route::middleware(['api.mahasiswa'])->group(function () {

    // Route untuk mengecek status tiket berdasarkan no_tiket
    // Metode: POST
    // URL: /api/check-ticket-status
    Route::post('/check-ticket-status', [MahasiswaTicketController::class, 'searchTicket']);

});

// Opsional: Jika Anda ingin endpoint untuk login atau data user
// Route::post('/login', [AuthController::class, 'login']);