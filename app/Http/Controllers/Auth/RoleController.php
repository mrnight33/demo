<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Validator\RoleValidatorController;

use Request;
use Session;

class RoleController extends Controller{

    public function createNewRole(){
        $userId = Session::get('userId');
        $name = Session::get('name');
        $role_name = Request::input('name');
        $message = RoleValidatorController::validateAddRole($role_name);
        if($message!==true){
            return $message;
        }
        $desc = Request::input('desc')?:null;
        return null;
    }
}
