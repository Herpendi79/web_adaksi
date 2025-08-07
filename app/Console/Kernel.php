<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\CleanupPendingAnggota::class,
    ];


    protected function schedule(Schedule $schedule)
    {
        \Log::info('Schedule ran at: ' . now()); // ← Tambahkan baris ini

        $schedule->command('app:cleanup-pending-anggota')->everyFiveMinutes();
    }



    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
