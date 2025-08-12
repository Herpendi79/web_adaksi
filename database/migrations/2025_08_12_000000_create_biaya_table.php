<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaModel extends Model
{
    protected $table = 'biaya';
    protected $primaryKey = 'id';
    protected $fillable = [
        'keterangan',
        'nominal',
        'berlaku_mulai',
        'berlaku_sampai',
    ];

    public $timestamps = true;

}
