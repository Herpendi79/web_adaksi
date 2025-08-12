<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserExpired extends Authenticatable
{
  
    protected $table = 'users_tmp';
    protected $primaryKey = 'id_user';
    protected $fillable = [
        'role',
        'email',
        'password',
        'password_temporary',
        'no_hp',
    ];
    public $timestamps = true;

    public function anggota_tmp()
    {
        return $this->hasOne(AnggotaExpiredModel::class, 'id_user', 'id_user');
    }
}
