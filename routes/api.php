<?php

use App\Http\Controllers\AnggotaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripayCallbackController;

Route::post('/callback', [TripayCallbackController::class, 'handle']);

Route::post('/midtrans-callback', [AnggotaController::class, 'callback']);
