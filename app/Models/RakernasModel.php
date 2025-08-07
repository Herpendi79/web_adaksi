<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RakernasModel extends Model
{
    protected $table = 'rakernas';
    protected $primaryKey = 'id_rk';
    protected $fillable = [
        'tema',
        'tempat',
        'tanggal_mulai',
        'tanggal_selesai',
        'biaya',
        'fasilitas',
        'sertifikat_depan',
        'sertifikat_belakang'
    ];

    public $timestamps = true;

    public function pendaftar()
    {
        return $this->hasMany(PendaftarRakernasModel::class, 'id_rk', 'id_rk');
    }
}
