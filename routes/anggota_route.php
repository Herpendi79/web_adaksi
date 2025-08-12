<?php
// Anggota Routes
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\WebinarController;
use App\Http\Controllers\RakernasController;
use App\Http\Controllers\AduanController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::prefix('anggota')
    ->middleware(['auth'])
    ->group(function () {
        // AnggotaController
        Route::controller(AnggotaController::class)->group(function () {
            Route::get('dashboard', 'dashboard')->name('anggota.dashboard');
            Route::get('profile', 'profile')->name('anggota.profile');
            Route::get('webinar', 'webinar')->name('anggota.webinar');
            Route::get('rakernas', 'showAllRakernas')->name('anggota.rakernas.index');
            Route::post('webinar/store_registrasi_anggota', 'store_registrasi_anggota')->name('store_registrasi_anggota');
            Route::post('daftar-webinar', 'daftarWebinar')->name('daftarWebinar');
            //Route::post('daftar-webinar', 'storeWebinar')->name('anggota.storeWebinar');
            Route::post('daftar-webinar-free', 'daftarWebinarFree')->name('daftarWebinarFree');
            Route::post('rakernas/store_registrasi_rakernas', 'store_registrasi_rakernas')->name('store_registrasi_rakernas');
            Route::get('profile/edit', 'editProfile')->name('anggota.profile.edit');
            Route::post('profile/edit', 'updateProfile')->name('anggota.profile.update');
            Route::get('download-kta', 'downloadKTA')->name('anggota.download_kta');
            Route::get('profile/edit_password', 'editPassword')->name('anggota.profile.edit_password');
            Route::post('profile/edit_password', 'updatePassword')->name('anggota.profile.update_password');
        });

        // AduanController
        Route::controller(AduanController::class)->group(function () {
            Route::get('aduan', 'showAllAduan')->name('aduan.index');
            Route::get('aduan/create', 'create')->name('aduan.create');
            Route::get('aduan/edit/{id}', 'edit')->name('aduan.edit');
            Route::put('aduan/{id}', 'update')->name('aduan.update');
            Route::post('aduan/hapus/{id}', 'hapus')->name('aduan.hapus');
            Route::post('aduan/store', 'store')->name('aduan.store');
            Route::post('aduan/store_tanggapan', 'store_tanggapan')->name('aduan.store_tanggapan');
        });

        // Webinar & Rakernas
        Route::post('storeWebinar', [WebinarController::class, 'storeWebinar'])->name('storeWebinar');
        Route::get('sertifikat/{id_wb}/{id?}', [WebinarController::class, 'sertifikat'])->name('anggota.sertifikat');
        Route::get('sertifikat_rakernas/{id_prk}/{id?}', [RakernasController::class, 'sertifikat_rakernas'])->name('anggota.sertifikat_rakernas');
        //Route::get('/validasi-pembayaran-rakernas/{id_prk}', [RakernasController::class, 'validasiPendaftar'])->name('anggota.validasiPendaftar');
    });

Route::get('/anggota_page/bayar/{snapToken}', function ($snapToken) {
    $user = Auth::user(); // Mendapatkan user yang sedang login

    if (!$user) {
        return redirect('/anggota/rakernas');
    }

    $idUser = $user->id_user; // Ambil id_user dari user login

    $pendaftar = \App\Models\PendaftarRakernasModel::where('snap', $snapToken)
        ->where('id_user', $idUser)
        ->first();

    if (!$pendaftar) {
        return redirect('/anggota/rakernas');
    }

    return view('anggota_page.rakernas.bayar', [
        'snapToken' => $snapToken,
        'biaya' => $pendaftar->biaya,
        'nama' => optional($pendaftar->anggota)->nama_anggota ?? 'Bapak/Ibu Dosen',
        'status' => $pendaftar->status,
        'id_prk' => $pendaftar->id_prk,
    ]);
})->middleware('auth')->name('anggota.bayar');

Route::get('/validasi-pembayaran-rakernas/{snapToken}', [AnggotaController::class, 'validasiBySnapRakernas'])->name('anggota.validasi');
Route::get('/cek-expired-rakernas/{snapToken}', [AnggotaController::class, 'cekDanHapusJikaExpiredRakernas']);

Route::prefix('tabulasi')
    ->controller(AnggotaController::class)
    ->group(function () {
        Route::get('anggota/', 'TabulasiAnggotaPage')->name('anggota.tabulasi.index');
    });

Route::get('/rekap/excel', [AnggotaController::class, 'exportGabungan'])->name('rekap.excel');

Route::get('download-kta-1', [AnggotaController::class, 'downloadKTA_1'])
    ->name('anggota.download_kta_1');

Route::match(['get', 'post'], '/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/anggota/webinar/pembayaran/{snapToken}', function ($snapToken) {
    $anggota = \App\Models\PendaftarExtModel::where('snap', $snapToken)->first();

    return view('anggota_page.webinar.pembayaran', [
        'snapToken' => $snapToken,
        'biaya' => $anggota?->biaya ?? 0,
        'nama' => $anggota?->nama ?? 'Bapak/Ibu Dosen',
        'id_pwe' => $anggota?->id_pwe ?? null,
        'token' => $anggota?->token ?? null,
        'no_urut' => $anggota?->no_urut ?? null,
    ]);
})->name('anggota.webinar.pembayaran');

Route::get('/validasi-pembayaran-webinar/{snapToken}', [WebinarController::class, 'validasiBySnap'])->name('anggota.webinar.validasi');
