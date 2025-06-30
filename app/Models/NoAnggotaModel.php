<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoAnggotaModel extends Model
{
    // Nama tabel yang digunakan
    protected $table = 'no_anggota';

    // Jika kamu tidak pakai kolom created_at / updated_at
    public $timestamps = false;

    // Kolom yang bisa diisi (opsional tergantung kebutuhan)
    protected $fillable = [
        'id_card',
    ];

    // Jika kolom primary key bukan 'id' (optional)
    // protected $primaryKey = 'id';

    // Jika primary key bukan auto-increment (optional)
    // public $incrementing = false;

    // Jika id_card adalah string (optional, sesuaikan dengan struktur DB)
    // protected $keyType = 'string';
}
