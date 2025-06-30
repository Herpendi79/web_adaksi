<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FasilitasModel extends Model
{
    protected $table = 'fasilitas';
    protected $primaryKey = 'id_fas';
    protected $fillable = [
        'id_wb',
        'nama',
        'link'
    ];
    public $timestamps = true;
    
    public function webinar()
    {
        return $this->belongsTo(WebinarModel::class, 'id_wb', 'id_wb');
    }

}
