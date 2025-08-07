<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiRakernasModel extends Model
{
    protected $table = 'absensi_rakernas';
    protected $primaryKey = 'id_absen';
    protected $fillable = [
        'id_prk',
        'kehadiran'
    ];
    public $timestamps = true;

    public function AbsenPendaftar()
    {
        return $this->belongsTo(PendaftarRakernasModel::class, 'id_prk', 'id_prk');
    }
}
