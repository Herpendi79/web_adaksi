<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftarExtModel extends Model
{
    protected $table = 'pendaftar_webinar_ext';
    protected $primaryKey = 'id_pwe';
    protected $fillable = [
        'id_wb',
        'nama',
        'email',
        'no_hp',
        'nip',
        'status',
        'home_base',
        'provinsi',
        'biaya',
        'keterangan',
        'bukti_tf',
        'token',
        'snap',
        'order_id',
        'no_urut',
        'no_sertifikat'
    ];
    public $timestamps = true;
    
    public function webinar()
    {
        return $this->belongsTo(WebinarModel::class, 'id_wb', 'id_wb');
    }

}
