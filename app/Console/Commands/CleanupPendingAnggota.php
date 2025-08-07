<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupPendingAnggota extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-pending-anggota';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $batasMenit = 30; // misalnya expired setelah 30 menit

        $anggotaPending = \App\Models\AnggotaModel::where('status_anggota', 'pending')
            ->where('created_at', '<', now()->subMinutes($batasMenit))
            ->get();

        foreach ($anggotaPending as $anggota) {
            $idUser = $anggota->id_user;

            \App\Models\User::where('id_user', $idUser)->delete();
            $anggota->delete();
            

            \Log::info('Auto-deleted anggota pending (tidak pernah dibayar)', [
                'order_id' => $anggota->order_id,
                'id_user' => $idUser,
            ]);
        }
    }
}
