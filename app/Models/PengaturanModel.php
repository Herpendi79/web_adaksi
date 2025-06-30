<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanModel extends Model
{
    protected $table = 'pengaturan';
    protected $primaryKey = 'id_pengaturan';

    protected $fillable = [
        'no_awal',
        'no_tengah',
    ];

    public $timestamps = true;
}
