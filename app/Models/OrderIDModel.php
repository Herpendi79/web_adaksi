<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderIDModel extends Model
{
    protected $table = 'tb_order';
    protected $primaryKey = 'id';
    protected $fillable = [
        'order_id',
    ];
    public $timestamps = true;
}
