<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


// Auth Routes
Route::controller(AuthController::class)
    ->group(function () {
        Route::get('login', 'login')->name('login');
        Route::post('login', 'login');
        Route::get('logout', 'logout')->name('logout');
        Route::post('logout', 'logout')->name('logout');
        // forget password
        Route::get('forget-password', 'forgetPassword')->name('forget.password');
        Route::post('forget-password', 'forgetPassword');
        Route::get('reset-password/{token}', 'resetPassword')->name('reset.password');
        Route::post('reset-password', 'resetPassword');
    });


require __DIR__ . '/admin_route.php';
require __DIR__ . '/guest_route.php';
require __DIR__ . '/anggota_route.php';

Route::get('/tampil-anggota', [App\Http\Controllers\AnggotaController::class, 'tampilAnggota']);

// 404 Not Found
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
