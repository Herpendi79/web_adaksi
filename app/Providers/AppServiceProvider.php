<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Models\AnggotaModel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(\Barryvdh\DomPDF\ServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['welcome', 
        'guest_page.info-munas-pertama',
        'guest_page.launching-dpp',
        'guest_page.obe-ai',
        'guest_page.pengisian-kinerja',
        'guest_page.pola-riset',
        'guest_page.profesor-loa',
        'guest_page.seminar-pembahasan-tukin',
        'guest_page.ss-menjaga-api-di-dua-dunia',
        'guest_page.implementasi-permen',
        'webinar'
        ], function ($view) {
       // $anggota = AnggotaModel::orderBy('no_urut', 'desc')->first();
        //$noUrutTerakhir = $anggota ? $anggota->no_urut : 0;
       // $view->with('noUrutTerakhir', $noUrutTerakhir);
        
        $noUrutTerakhir = AnggotaModel::where('status_anggota', 'aktif')->count();
        $view->with('noUrutTerakhir', $noUrutTerakhir);
    });
    
    View::composer('admin_page.main.dashboard', function ($view) {
        $jumlahPerProvinsi = AnggotaModel::select('provinsi', DB::raw('count(*) as total'))
            ->groupBy('provinsi')
            ->orderBy('total', 'desc')
            ->get();

        $view->with('jumlahPerProvinsi', $jumlahPerProvinsi);
    });
    }
}
