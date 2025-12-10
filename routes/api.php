<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TiketApiController;
use App\Http\Controllers\Api\ApiAuthController;

Route::post('/auth/login', [ApiAuthController::class, 'login']);
Route::post('/auth/google-mobile', [ApiAuthController::class, 'googleLoginMobile']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tiket/my-tickets', [TiketApiController::class, 'index']);
    Route::post('/tiket/{id}/komentar', [TiketApiController::class, 'storeKomentar']);
    Route::get('/tiket/{id_or_no_tiket}', [TiketApiController::class, 'show']);
});
Route::get('/tiket/{id_or_no_tiket}', [TiketApiController::class, 'show']);