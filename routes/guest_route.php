<?php

use App\Http\Controllers\AnggotaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebinarController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/real-daftar-anggota', function () {
  return view('guest_page.real_anggota_daftar_form');
  //  return view('guest_page.anggota_daftar_form');
});

Route::get('/daftar-anggota', function () {
    return view('guest_page.anggota_daftar_form');
});

Route::get('/login', function () {
    return view('auth.login');
});


Route::get('/webinar', [WebinarController::class, 'index'])->name('webinar.index');

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


Route::prefix('anggota')
->controller(AnggotaController::class)
->group(function () {
    Route::post('/daftar', 'store')->name('anggota.store');
});
