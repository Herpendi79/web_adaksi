<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekeningModel extends Model
{
    protected $table = 'rekening';
    protected $primaryKey = 'id_rek';
    protected $fillable = [
        'nama_bank',
        'no_rek',
        'atas_nama'
    ];

    public $timestamps = true;

        public function rekening()
    {
        return $this->hasMany(WebinarModel::class, 'id_rek', 'id_rek');
    }
        public function rekening_rakernas()
    {
        return $this->hasMany(RakernasModel::class, 'id_rek', 'id_rek');
    }
}
