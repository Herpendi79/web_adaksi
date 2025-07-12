<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftarRakernasModel extends Model
{
    protected $table = 'pendaftar_rakernas';
    protected $primaryKey = 'id_prk';
    protected $fillable = [
        'id_rk',
        'bukti_tf',
        'keterangan',
        'status',
        'qrcode'
    ];
    public $timestamps = true;

    public function rakernas()
    {
        return $this->belongsTo(RakernasModel::class, 'id_rk', 'id_rk');
    }

    public function anggota()
    {
        return $this->belongsTo(AnggotaModel::class, 'id_user', 'id_user');
    }

}
