<?php

namespace App\Http\Controllers\validator;

use Validator;
use App\Http\Controllers\Controller;

class RoleValidatorController extends Controller{

    public static function validateAddRole($name){
        $validator = Validator::make(
            [
                'name'=>$name
            ],
            [
                'name'=>'required|unique:roles'
            ],
            [
                'required'=>':attribute不能为空',
                'unique' =>':attribute已存在'
            ],
            [
                'name'=>'角色名'
            ]
        );
        if ($validator->fails()) {
            $warnings = $validator->messages()->toArray();
            $desc = ['error' => 94, 'desc' => '参数错误'];
            $result = array_merge($desc, $warnings);
            return $result;
        }
        return true;
    }
}
