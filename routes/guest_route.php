<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebinarController;
use App\Http\Controllers\TripayController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TripayCallbackController;
use App\Http\Middleware\VerifyCsrfToken;

//Route::get('/', function () {
//  return view('welcome');
//});



/*Route::get('/daftar-anggota-adaksi', function () {
    return view('guest_page.anggota_daftar_adaksi');
    //  return view('guest_page.anggota_daftar_form');
}); */
/*Route::get('/real-daftar-anggota', function () {
    return view('guest_page.real_anggota_daftar_form');
});*/

Route::get('/daftar-anggota', function () {
    return view('guest_page.anggota_tetap_daftar_form');
});

Route::get('/daftar-anggota-tetap', function () {
    return view('guest_page.anggota_tetap_daftar_form');
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

    // Route untuk anggota tetap dengan ambil order_id dari tabel
Route::prefix('anggota')
    ->controller(AnggotaController::class)
    ->group(function () {
        Route::post('/daftar-anggota-tetap', 'store')->name('anggota.store_anggota_tetap');
    });



Route::get('/anggota/pembayaran/{snapToken}', function ($snapToken) {
    $anggota = \App\Models\AnggotaModel::where('snap', $snapToken)->first();

    return view('guest_page.pembayaran', [
        'snapToken' => $snapToken,
        'biaya' => $anggota?->biaya ?? 0,
        'nama' => $anggota?->nama_anggota ?? 'Bapak/Ibu Dosen',
        'id_user' => $anggota?->id_user ?? null,
        'status_anggota' => $anggota?->status_anggota ?? 'tidak_ada',
    ]);
})->name('anggota.pembayaran');

Route::get('/validasi-pembayaran/{snapToken}', [AnggotaController::class, 'validasiBySnap'])->name('anggota.validasi');
Route::get('/cek-expired/{snapToken}', [AnggotaController::class, 'cekDanHapusJikaExpired']);



//route Tripay
Route::post('daftar_tripay', [TripayController::class, 'store_anggota'])->name('store_anggota');
Route::get('/bayar_anggota/{snapToken}', [TripayController::class, 'bayar'])->name('bayar_anggota');
Route::post('transaction', [TransactionController::class, 'store_pembayaran'])->name('transaction.store_pembayaran');

Route::get('bayar_anggota_show/{reference}', function ($reference) {
    $anggota = \App\Models\AnggotaModel::where('order_id', $reference)->first();

    $tripay = new TripayController;
    $detail = $tripay->detail_transaction($reference);

    return view('guest_page.bayar_anggota_show', [
        'detail' => $detail,
        'reference' => $reference,
        'biaya' => $anggota?->biaya ?? 0,
        'nama' => $anggota?->nama_anggota ?? 'Bapak/Ibu Dosen',
        'id_user' => $anggota?->id_user ?? null,
        'status_anggota' => $anggota?->status_anggota ?? 'tidak_ada',
    ]);
})->name('bayar_anggota_show');

Route::get('/cek-status/{reference}', [TripayController::class, 'cek_status']);

Route::get('/status-transaksi/{reference}', [TripayController::class, 'status_ajax']);

//Route::get('/cek-expired/{id_user}', [TripayController::class, 'hapusJikaExpired']);
