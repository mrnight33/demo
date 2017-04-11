<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RoleModel extends Model
{
    protected $table = 'roles';

    protected $hidden = ['created_at','updated_at','pivot'];

    public function users(){
        return $this->belongsToMany('App\Model\UserModel','user_role','role_id','user_id');
    }

    public function permissions(){
        return $this->belongsToMany('App\Model\PermissionModel','permission_role','role_id','permission_id');
    }

}
