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
    ->controller(AnggotaController::class)
    ->group(function () {
        Route::get('dashboard', [AnggotaController::class, 'dashboard'])->name('anggota.dashboard');
        Route::get('profile', [AnggotaController::class, 'profile'])->name('anggota.profile');
        Route::get('webinar', [AnggotaController::class, 'webinar'])->name('anggota.webinar');
        Route::get('rakernas', 'showAllRakernas')->name('anggota.rakernas.index');
        Route::get('aduan', [AduanController::class, 'showAllAduan'])->name('aduan.index');
        Route::get('create', [AduanController::class, 'create'])->name('aduan.create');
        Route::get('edit/{id}', [AduanController::class, 'edit'])->name('aduan.edit');
        Route::put('{id}', [AduanController::class, 'update'])->name('aduan.update');
        Route::post('aduan/hapus/{id}', [AduanController::class, 'hapus'])->name('aduan.hapus');
        Route::get('edit/{id}', [AduanController::class, 'edit'])->name('aduan.edit');
        Route::post('store', [AduanController::class, 'store'])->name('aduan.store');
        Route::post('store_tanggapan', [AduanController::class, 'store_tanggapan'])->name('aduan.store_tanggapan');
        Route::post('webinar/store_registrasi_anggota', [AnggotaController::class, 'store_registrasi_anggota'])->name('store_registrasi_anggota');
        Route::post('rakernas/store_registrasi_rakernas', [AnggotaController::class, 'store_registrasi_rakernas'])->name('store_registrasi_rakernas');
        Route::get('profile/edit', [AnggotaController::class, 'editProfile'])->name('anggota.profile.edit');
        Route::post('profile/edit', [AnggotaController::class, 'updateProfile'])->name('anggota.profile.update');
        Route::get('download-kta', [AnggotaController::class, 'downloadKTA'])->name('anggota.download_kta');
        Route::get('sertifikat/{id_wb}/{id?}', [WebinarController::class, 'sertifikat'])->name('anggota.sertifikat');
        Route::get('sertifikat_rakernas/{id_prk}/{id?}', [RakernasController::class, 'sertifikat_rakernas'])->name('anggota.sertifikat_rakernas');
        Route::get('profile/edit_password', [AnggotaController::class, 'editPassword'])->name('anggota.profile.edit_password');
        Route::post('profile/edit_password', [AnggotaController::class, 'updatePassword'])->name('anggota.profile.update_password');
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
