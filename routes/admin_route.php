<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebinarController;

Route::prefix('admin')
    ->middleware(['access:admin|superadmin'])
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
                Route::post('/admin/webinar', [WebinarController::class, 'store'])->name('webinar.store');
                Route::post('publish/{id}', 'publish')->where('id', '[0-9]+');
                // Route::get('edit','edit/{id}')->name('webinar.edit');
                // Route::get('edit/{id}', 'edit')->name('webinar.edit');
                Route::get('edit/{id}', 'edit')->name('admin.webinar.edit');
                // Route::get('ubah/{id_wb}', 'ubah')->name('admin.webinar.ubah');
                // Route::get('edit/{id}', 'edit')->where('id', '[0-9]+');
                //Route::get('/admin/webinar/edit/{id}', [WebinarController::class, 'edit'])->name('webinar.edit');

                Route::put('admin/webinar/{id}', [WebinarController::class, 'update'])->name('webinar.update');
                Route::get('/admin/webinar', [WebinarController::class, 'showAllWebinar'])->name('webinar.index');

                Route::post('validasi/{id}', 'validasi')->where('id', '[0-9]+');
            });

        Route::prefix('calonanggota')
            ->controller(AnggotaController::class)
            ->group(function () {
                Route::get('/', 'showAllCalonAnggota')->name('calonanggota.index');
                Route::post('validasi/{id}', 'validasi')->where('id', '[0-9]+'); // ✅ Tambahan ini penting
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
    });
