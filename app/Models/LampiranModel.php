<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LampiranModel extends Model
{
    protected $table = 'lampiran';
    protected $primaryKey = 'id_lamp';
    protected $fillable = [
        'id_ad',
        'lampiran'
    ];
    public $timestamps = true;

    public function aduan()
    {
        return $this->belongsTo(AduanModel::class, 'id_ad', 'id_ad');
    }
}
