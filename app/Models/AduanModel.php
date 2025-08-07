<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AduanModel extends Model
{
    protected $table = 'aduan';
    protected $primaryKey = 'id_ad';
    protected $fillable = [
        'id_user',
        'kategori',
        'judul',
        'deskripsi',
        'status'
    ];
    public $timestamps = true;

    public function anggota()
    {
        return $this->belongsTo(AnggotaModel::class, 'id_user', 'id_user');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
    public function tanggapan()
    {
        return $this->hasMany(TanggapanModel::class, 'id_ad', 'id_ad');
    }
    public function lampiran()
    {
        return $this->hasMany(LampiranModel::class, 'id_ad', 'id_ad');
    }
}
