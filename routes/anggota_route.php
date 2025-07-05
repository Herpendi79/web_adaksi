<?php
// Anggota Routes
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\WebinarController;
use Illuminate\Support\Facades\Route;

Route::prefix('anggota')
    ->middleware(['auth'])
    ->controller(AnggotaController::class)
    ->group(function () {
        Route::get('dashboard', function () {
            return view('anggota_page.main.dashboard');
        })->name('anggota.dashboard');

        Route::get('profile', [AnggotaController::class, 'profile'])->name('anggota.profile');
        Route::get('webinar', [AnggotaController::class, 'webinar'])->name('anggota.webinar');
        Route::post('webinar/store_registrasi_anggota', [AnggotaController::class, 'store_registrasi_anggota'])->name('store_registrasi_anggota'); 
        Route::get('profile/edit', [AnggotaController::class, 'editProfile'])->name('anggota.profile.edit');
        Route::post('profile/edit', [AnggotaController::class, 'updateProfile'])->name('anggota.profile.update');
        Route::get('download-kta', [AnggotaController::class, 'downloadKTA'])->name('anggota.download_kta');
        Route::get('sertifikat/{id}', [WebinarController::class, 'sertifikat'])->name('anggota.sertifikat');
        Route::get('profile/edit_password', [AnggotaController::class, 'editPassword'])->name('anggota.profile.edit_password');
        Route::post('profile/edit_password', [AnggotaController::class, 'updatePassword'])->name('anggota.profile.update_password');

        // Route::get('')
        // Add more anggota-specific routes here
    });
Route::get('download-kta-1', [AnggotaController::class, 'downloadKTA_1'])
    ->name('anggota.download_kta_1');
