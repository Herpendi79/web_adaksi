<?php

use App\Http\Controllers\AduanController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebinarController;
use App\Http\Controllers\RakernasController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BiayaController;

Route::prefix('admin')
    ->middleware(['access:admin|superadmin|hukum'])
    ->group(function () {
        // route untuk admin pengaturan
        Route::get('setting', [DashboardController::class, 'setting'])->name('admin.setting');
        Route::post('setting', [DashboardController::class, 'edit_pass'])->name('admin.setting.edit');

        // Admin Dashboard
        Route::get('/', function () {

            return redirect()->route('admin.dashboard');
        });

        Route::get('pengaturan', [DashboardController::class, 'pengaturan'])->name('admin.pengaturan');
        Route::post('pengaturan', [DashboardController::class, 'edit'])->name('admin.pengaturan.edit');


        Route::prefix('dashboard')
            ->controller(DashboardController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::get('tambah', 'create');
                Route::post('tambah', 'store');
                Route::get('edit/{id}', 'edit')->where('id', '[0-9]+');
                Route::post('edit/{id}', 'update')->where('id', '[0-9]+');
                Route::get('hapus/{id}', 'destroy')->where('id', '[0-9]+');
                Route::get('detail/{id}', 'show')->where('id', '[0-9]+');
                Route::post('validasi/{id}', 'validasi')->where('id', '[0-9]+');
            });
        // Anggota Manajement
        Route::prefix('anggota')
            ->controller(AnggotaController::class)
            ->group(function () {
                Route::get('/', 'showAll');
                Route::get('tambah', 'create');
                Route::post('tambah', 'store');
                Route::get('edit/{id}', 'edit')->where('id', '[0-9]+');
                Route::post('edit/{id}', 'update')->where('id', '[0-9]+');
                Route::get('hapus/{id}', 'destroy')->where('id', '[0-9]+');
                Route::get('detail/{id}', 'show')->where('id', '[0-9]+');
                Route::post('validasi/{id}', 'validasi')->where('id', '[0-9]+');
                Route::post('edit-pengaturan/{id}', 'update_pengaturan')->where('id', '[0-9]+');
                // import excel anggota
                Route::get('import', 'import')->name('anggota.import');
                Route::post('import', 'importStore')->name('anggota.import.store');
            });
        // Webinar Manajement
        Route::prefix('webinar')
            ->controller(WebinarController::class)
            ->group(function () {
                Route::get('/', 'showAllWebinar');
                Route::get('create', 'create')->name('webinar.create');
                //  Route::post('/admin/webinar', [WebinarController::class, 'store'])->name('webinar.store');
                Route::post('/', 'store')->name('webinar.store');
                Route::post('publish/{id}', 'publish')->where('id', '[0-9]+');
                Route::post('hapus/{id}', 'hapus')->where('id', '[0-9]+');
                Route::post('selesai/{id}', 'selesai')->where('id', '[0-9]+');
                Route::get('edit/{id}', 'edit')->name('admin.webinar.edit');
                Route::put('admin/webinar/{id}', [WebinarController::class, 'update'])->name('webinar.update');
                Route::get('/admin/webinar', [WebinarController::class, 'showAllWebinar'])->name('webinar.index');
                Route::get('pendaftar/{id}', [WebinarController::class, 'pendaftar'])->name('webinar.pendaftar');
                Route::post('validasi/{id}', 'validasi')->where('id', '[0-9]+');
                Route::post('validasiPendaftar/{id}', 'validasiPendaftar')->where('id', '[0-9]+');
            });
        // Rekening Manajement
        Route::prefix('rekening')
            ->controller(RekeningController::class)
            ->group(function () {
                Route::get('/', 'showAllRekening');
                Route::get('create', 'create')->name('rekening.create');
                Route::post('/', 'store')->name('rekening.store');
                Route::post('hapus/{id}', 'hapus')->where('id', '[0-9]+');
                Route::get('edit/{id}', 'edit')->name('admin.rekening.edit');
                Route::put('{id}', 'update')->name('rekening.update');
            });

        // Biaya Manajement
        Route::prefix('biaya')
            ->controller(BiayaController::class)
            ->group(function () {
                Route::get('/', 'showAllBiaya');
                Route::post('/', 'store')->name('biaya.store');
                //Route::put('{id}', 'update')->name('biaya.update');
                Route::post('update/{id}', 'update');
            });

        // Rakernas Manajement
        Route::prefix('rakernas')
            ->controller(RakernasController::class)
            ->group(function () {
                Route::get('/', 'showAllRakernas')->name('rakernas.index');
                Route::get('create', 'create')->name('rakernas.create');
                Route::post('/', 'store')->name('rakernas.store');
                Route::get('edit/{id}', 'edit')->name('admin.rakernas.edit');
                Route::get('absensi/{id}', 'absensi')->name('rakernas.absensi');
                //  Route::get('absensi/{id}', [RakernasController::class, 'absensi'])->name('admin.rakernas.absensi');
                Route::get('absensi_create/{id}', 'absensi_create')->name('admin.rakernas.absensi_create');
                Route::post('check-qrcode', 'checkQrCode')->name('rakernas.check_qrcode');
                Route::post('simpan-absensi', 'simpanAbsensi')->name('rakernas.simpan_absensi');
                Route::post('store-absensi', 'store_absensi')->name('rakernas.store_absensi'); // jika ingin route store absensi terpisah
                Route::put('{id}', 'update')->name('rakernas.update');
                Route::post('publish/{id}', 'publish')->where('id', '[0-9]+');
                Route::post('hapus/{id}', 'hapus')->where('id', '[0-9]+');
                Route::post('selesai/{id}', 'selesai')->where('id', '[0-9]+');
                Route::get('pendaftar/{id}', 'pendaftar')->name('rakernas.pendaftar');
                Route::post('validasi/{id}', 'validasi')->where('id', '[0-9]+');
                Route::post('validasiPendaftar/{id}', 'validasiPendaftar')->where('id', '[0-9]+');
                Route::get('export-pendaftar/{id}', 'exportPendaftar')->name('rakernas.export_pendaftar');
            });

        // Aduan Manajement
        Route::prefix('aduan')
            ->controller(AduanController::class)
            ->group(function () {
                Route::get('/', 'showAllAduanOnAdmin');
                Route::post('store_tanggapan_admin', 'store_tanggapan_admin')->name('aduan.store_tanggapan_admin');
                Route::post('selesai/{id}', 'selesai')->name('selesai')->where('id', '[0-9]+');
            });

        Route::post('store_tanggapan', [AduanController::class, 'store_tanggapan'])->name('aduan.store_tanggapan');

        Route::prefix('calonanggota')
            ->controller(AnggotaController::class)
            ->group(function () {
                Route::get('/', 'showAllCalonAnggota')->name('calonanggota.index');
                Route::post('validasi/{id}', 'validasi')->where('id', '[0-9]+'); // ✅ Tambahan ini penting
            });

        Route::prefix('expiredanggota')
            ->controller(AnggotaController::class)
            ->group(function () {
                Route::get('/', 'showAllExpiredAnggota')->name('expiredanggota.index');
                Route::post('validasiExpired/{id}', 'validasiExpired')->where('id', '[0-9]+'); // ✅ Tambahan ini penting
                Route::get('/admin/expiredanggota', 'showAllExpiredAnggota')->name('expiredanggota.index');
            });

        Route::prefix('importanggota')
            ->controller(AnggotaController::class)
            ->group(function () {
                Route::get('/', 'ImportAnggota')->name('importanggota.index');
                Route::post('validasi/{id}', 'validasi')->where('id', '[0-9]+'); // ✅ Tambahan ini penting
            });

        Route::prefix('setting')
            ->controller(AuthController::class)
            ->group(function () {
                Route::get('setting', [AuthController::class, 'editProfile'])->name('setting.index');
                //Route::post('profile/edit', [AnggotaController::class, 'updateProfile'])->name('anggota.profile.update');
            });

        Route::prefix('rekap')
            ->controller(AnggotaController::class)
            ->group(function () {
                Route::get('/', 'RekapAnggota')->name('rekap.index');
                Route::get('/rekap-anggota', [AnggotaController::class, 'RekapAnggota'])->name('rekap.anggota');
            });

        Route::prefix('tabulasi')
            ->controller(AnggotaController::class)
            ->group(function () {
                Route::get('/', 'TabulasiAnggota')->name('tabulasi.index');
                Route::get('/tabulasi-anggota', [AnggotaController::class, 'TabulasiAnggota'])->name('tabulasi.anggota');
            });

        Route::get('/export-anggota', [AnggotaController::class, 'export'])->name('export.anggota');

        //  Route::get('/rekap-per-provinsi/excel', [AnggotaController::class, 'exportRekapPerProvinsi'])->name('rekap-per-provinsi.excel');
        Route::get('/rekap/excel', [AnggotaController::class, 'exportGabungan'])->name('rekap.excel');

        Route::match(['get', 'post'], '/login', [AuthController::class, 'login'])->name('login');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
