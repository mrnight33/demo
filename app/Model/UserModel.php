<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{

    protected $table = 'users';

    protected $hidden = ['pwd','created_at','updated_at','pivot'];

    public function roles(){
        return $this->belongsToMany('App\Model\RoleModel','user_role','user_id','role_id');
    }
}
