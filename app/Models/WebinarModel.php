<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebinarModel extends Model
{
    protected $table = 'webinar';
    protected $primaryKey = 'id_wb';
    protected $fillable = [
        'judul',
        'deskripsi',
        'hari',
        'tanggal_mulai',
        'tanggal_selesai',
        'pukul',
        'link_zoom',
        'bayar_free',
        'biaya_anggota_aktif',
        'biaya_anggota_non_aktif',
        'biaya_non_anggota',
        'moderator',
        'flyer',
        'sertifikat_depan',
        'sertifikat_belakang',
        'status'
    ];
    public $timestamps = true;
    
    public function fasilitas()
    {
        return $this->hasMany(FasilitasModel::class, 'id_wb', 'id_wb');
    }

    

}
