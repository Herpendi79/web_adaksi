<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TanggapanModel extends Model
{
    protected $table = 'tanggapan';
    protected $primaryKey = 'id_tang';
    protected $fillable = [
        'id_ad',
        'isi_tanggapan',
        'lampiran',
        'pemilik',
    ];
    public $timestamps = true;

    public function tanggapan()
    {
        return $this->belongsTo(AduanModel::class, 'id_ad', 'id_ad');
    }
}
