<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TiketApiController;

Route::get('/tiket/{id_or_no_tiket}', [TiketApiController::class, 'show']);