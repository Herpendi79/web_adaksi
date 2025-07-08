<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SertifikatModel extends Model
{
    protected $table = 'sertifikat';
    protected $primaryKey = 'id_sert';
    protected $fillable = [
        'id_wb',
        'no_surat',
        'angkatan',
        'unit'
    ];
    public $timestamps = true;
    
    public function webinar()
    {
        return $this->belongsTo(WebinarModel::class, 'id_wb', 'id_wb');
    }

}
