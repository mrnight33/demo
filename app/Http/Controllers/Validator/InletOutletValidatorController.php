<?php
/**
 * Created by PhpStorm.
 * User: Typer_work
 * Date: 2017/4/13
 * Time: 9:53
 */

namespace App\Http\Controllers\validator;

use Validator;
use App\Http\Controllers\Controller;

class InletOutletValidatorController extends Controller {

    public static function validateAddNewInletAndOutlet($name, $longitude,$latitude,$openTime,$closeTime,$status){
        $validator = Validator::make(
            [
                'name' =>$name,
                'longitude'=>$longitude,
                'latitude'=>$latitude,
                'openTime'=>$openTime,
                'closeTime'=>$closeTime,
                'status'=>$status
            ],
            [
                'name'=>'required|between:2,16',
                'longitude'=>'required|numeric',
                'latitude'=>'required|numeric',
                'openTime'=>array('regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'),
                'closeTime'=>array('regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'),
                'status'=>'required|boolean|integer'
            ],
            [
                'required'=>':attribute不能为空',
                'between'=>':attribute长度必须在2-16位之间',
                'numeric'=>'：attribute必须是数字',
                'regex'=>':attribute格式错误',
                'boolean'=>':attribute只能是0或1',
                'integer'=>':attribute只能是0或1'
            ],
            [
                'name'=>'名称',
                'longitude'=>'经度',
                'latitude'=>'纬度',
                'openTime'=>'开放时间',
                'closeTime'=>'关闭时间',
                'status'=>'状态'
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

    public static function validateOpenAndCloseTime($id, $openTime, $closeTime){
        $validator = Validator::make(
            [
                'id'=>$id,
                'open_time'=>$openTime,
                'close_time'=>$closeTime
            ],
            [
                'id'=>'required|numeric',
                'open_time'=>array('regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'),
                'close_time'=>array('regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/')
            ]
        );
        if ($validator->fails()) {
            return ['error' => 94, 'desc' => '参数错误'];
        }
        return true;
    }

    public static function validateUpdateInfo($id, $name, $longitude, $latitude){
        $validator = Validator::make(
            [
                'id'=>$id,
                'name' =>$name,
                'longitude'=>$longitude,
                'latitude'=>$latitude,
            ],
            [
                'id'=>'required|numeric',
                'name'=>'required|between:2,16',
                'longitude'=>'required|numeric',
                'latitude'=>'required|numeric',
            ]
        );
        if ($validator->fails()) {
            return ['error' => 94, 'desc' => '参数错误'];
        }
        return true;
    }

    public static function validateStatus($id,$status){
        $validator = Validator::make(
            [
                'id'=>$id,
                'status'=>$status
            ],
            [
                'id'=>'required|numeric',
                'status'=>'required|boolean|integer',
            ]
        );
        if ($validator->fails()) {
            return ['error' => 94, 'desc' => '参数错误'];
        }
        return true;
    }
}