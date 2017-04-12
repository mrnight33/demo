<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PermissionModel extends Model
{
    protected $table = 'permissions';

    protected $hidden = ['created_at','updated_at','pivot','desc'];

    public function roles(){
        return $this->belongsToMany('App\Model\RoleModel','permission_role','permission_id','role_id');
    }

    public function ScopeGetParent($pid){
        return $this->where('pid',$pid)->get()->toArray();
    }
}
