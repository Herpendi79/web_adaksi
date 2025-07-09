<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebinarController;

//Route::get('/', function () {
  //  return view('welcome');
//});

Route::get('/real-daftar-anggota', function () {
    return view('guest_page.real_anggota_daftar_form');
    //  return view('guest_page.anggota_daftar_form');
});

Route::get('/daftar-anggota', [WebinarController::class, 'daftar'])->name('daftar');

Route::get('/registrasi/{id}', [WebinarController::class, 'registrasi'])->name('registrasi');

Route::get('/login', function () {
    return view('auth.login');
});


Route::get('/fasilitas', function () {
    return view('fasilitas');
});

Route::post('/fasilitas', [AuthController::class, 'fasilitas'])->name('fasilitas');

Route::get('/fasilitas/result', [AuthController::class, 'fasilitasResult'])->name('fasilitas.result');

Route::get('/fasilitas/sertifikat/{id}', [AuthController::class, 'fasilitasSertifikat'])->name('fasilitas.sertifikat');

Route::post('/fasilitas/clear', [AuthController::class, 'clearSession'])->name('fasilitas.clear');


Route::get('/', [WebinarController::class, 'index'])->name('index');

Route::get('/agenda/{id}', [WebinarController::class, 'agenda'])->name('agenda');


Route::get('/about', function () {
    return view('guest_page.about');
});

Route::get('/contact', function () {
    return view('guest_page.contact');
});

Route::get('/advokasi', function () {
    return view('guest_page.advokasi');
});

Route::get('/launching-dpp', function () {
    return view('guest_page.launching-dpp');
});

Route::get('/ss-menjaga-api-di-dua-dunia', function () {
    return view('guest_page.ss-menjaga-api-di-dua-dunia');
});

Route::get('/info-munas-pertama', function () {
    return view('guest_page.info-munas-pertama');
});

Route::get('/seminar-pembahasan-tukin', function () {
    return view('guest_page.seminar-pembahasan-tukin');
});

Route::get('/obe-ai', function () {
    return view('guest_page.obe-ai');
});

Route::get('/produk-hukum', function () {
    return view('guest_page.produk-hukum');
});

Route::get('/pengisian-kinerja', function () {
    return view('guest_page.pengisian-kinerja');
});

Route::get('/profesor-loa', function () {
    return view('guest_page.profesor-loa');
});

Route::get('/pola-riset', function () {
    return view('guest_page.pola-riset');
});

Route::get('/implementasi-permen', function () {
    return view('guest_page.implementasi-permen');
});



Route::post('/registrasi', [WebinarController::class, 'store_registrasi'])->name('store_registrasi');


Route::prefix('anggota')
    ->controller(AnggotaController::class)
    ->group(function () {
        Route::post('/daftar', 'store')->name('anggota.store');
    });
