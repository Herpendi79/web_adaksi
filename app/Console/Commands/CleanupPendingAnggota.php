<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $batasMenit = 60; // misalnya expired setelah 30 menit

        $anggotaPending = \App\Models\AnggotaModel::where('status_anggota', 'pending')
            ->where('created_at', '<', now()->subMinutes($batasMenit))
            ->get();

        foreach ($anggotaPending as $anggota) {
            DB::beginTransaction();
            try {
                $idUser = $anggota->id_user;
                // Ambil data user
                $user = \App\Models\User::where('id_user', $idUser)->first();
                if ($user) {
                    // Jika email sudah ada di users_tmp, hapus dulu
                    DB::table('users_tmp')->where('email', $user->email)->delete();
                }
                // Jika nip_nipppk sudah ada di anggota_tmp, hapus dulu
                if ($anggota->nip_nipppk) {
                    DB::table('anggota_tmp')->where('nip_nipppk', $anggota->nip_nipppk)->delete();
                }
                // Insert ke users_tmp
                if ($user) {
                    DB::table('users_tmp')->insert([
                        'id_user' => $user->id_user,
                        'email' => $user->email,
                        'no_hp' => $user->no_hp,
                        'email_verified_at' => $user->email_verified_at,
                        'password' => $user->password,
                        'password_temporary' => $user->password_temporary,
                        'role' => $user->role,
                        'remember_token' => $user->remember_token,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at
                    ]);
                }
                // Insert ke anggota_tmp
                DB::table('anggota_tmp')->insert([
                    'id_anggota' => $anggota->id_anggota,
                    'id_user' => $anggota->id_user,
                    'no_urut' => $anggota->no_urut,
                    'id_card' => $anggota->id_card,
                    'nama_anggota' => $anggota->nama_anggota,
                    'nip_nipppk' => $anggota->nip_nipppk,
                    'status_dosen' => $anggota->status_dosen,
                    'homebase_pt' => $anggota->homebase_pt,
                    'provinsi' => $anggota->provinsi,
                    'foto' => $anggota->foto,
                    'status_anggota' => $anggota->status_anggota,
                    'snap' => $anggota->snap,
                    'order_id' => $anggota->order_id,
                    'biaya' => $anggota->biaya,
                    'bukti_tf_pendaftaran' => $anggota->bukti_tf_pendaftaran,
                    'keterangan' => $anggota->keterangan,
                    'tgl_keanggotaan' => $anggota->tgl_keanggotaan,
                    'created_at' => $anggota->created_at,
                    'updated_at' => $anggota->updated_at
                ]);
                // Hapus anggota dulu, baru user (hindari FK error)
                $anggota->delete();
                \App\Models\User::where('id_user', $idUser)->delete();
                DB::commit();
                Log::info('Auto-deleted anggota pending (tidak pernah dibayar)', [
                    'order_id' => $anggota->order_id,
                    'id_user' => $idUser,
                    'email' => $user ? $user->email : null,
                    'nip_nipppk' => $anggota->nip_nipppk,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Gagal auto-delete anggota pending: ' . $e->getMessage());
            }
        }
    }
}
