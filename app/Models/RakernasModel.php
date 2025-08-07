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
        'tanggal_tutup',
        'biaya',
        'biaya_non_pengurus',
        'kuota',
        'fasilitas',
        'sertifikat_depan',
        'sertifikat_belakang',
        'id_rek'
    ];

    public $timestamps = true;

    public function pendaftar()
    {
        return $this->hasMany(PendaftarRakernasModel::class, 'id_rk', 'id_rk');
    }
    public function rekening_rakernas()
    {
        return $this->belongsTo(RekeningModel::class, 'id_rek', 'id_rek');
    }
    public function hitungSisaKuotaAll()
    {
        $jumlah_pendaftar_valid = $this->pendaftar()
            ->where('pengurus', 'Anggota Biasa')
            ->count();

        return max(($this->kuota ?? 0) - $jumlah_pendaftar_valid, 0);
    }
    public function hitungSisaKuota()
    {
        $jumlah_pendaftar_valid = $this->pendaftar()
            ->where('status', 'valid')
            ->where('pengurus', 'Anggota Biasa')
            ->count();

        return max(($this->kuota ?? 0) - $jumlah_pendaftar_valid, 0);
    }
    public function hitungLimitDaftar()
    {
        $jumlah_pendaftar_valid = $this->pendaftar()
            ->where('status', 'valid')
            ->where('pengurus', 'Anggota Biasa')
            ->count();
        return max(($this->kuota ?? 0) - $jumlah_pendaftar_valid, 0);
    }
    public function hitungPendaftarValid()
    {
        return $this->pendaftar()
            ->where('status', 'valid')
            ->where('pengurus', 'Anggota Biasa')
            ->count();
    }
    public function hitungPendaftarUnValid()
    {
        return $this->pendaftar()
            ->where('status', 'pending')
            ->where('pengurus', 'Anggota Biasa')
            ->count();
    }
}
