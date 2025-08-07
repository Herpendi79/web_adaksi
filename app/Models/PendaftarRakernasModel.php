<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftarRakernasModel extends Model
{
    protected $table = 'pendaftar_rakernas';
    protected $primaryKey = 'id_prk';
    protected $fillable = [
        'id_rk',
        'id_user',
        'bukti_tf',
        'keterangan',
        'ukuran_baju',
        'status',
        'snap',
        'order_id',
        'biaya',
        'qrcode',
        'pengurus',
        'no_urut',
        'no_sertifikat'
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
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
    public function absensi()
    {
        return $this->hasOne(AbsensiRakernasModel::class, 'id_prk', 'id_prk');
    }
}
