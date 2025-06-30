<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftaranAnggotaModel extends Model
{
    protected $table = 'pendaftaran_anggota';
    protected $primaryKey = 'id_pendaftaran';

    protected $fillable = [
        'id_anggota',
        'bukti_transaksi',
        'status_pendaftaran',
    ];

    public $timestamps = true;
}
