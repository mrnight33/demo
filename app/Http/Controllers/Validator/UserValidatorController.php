<?php
/**
 * Created by PhpStorm.
 * User: Typer_work
 * Date: 2017/4/10
 * Time: 14:36
 */

namespace App\Http\Controllers\Validator;

use Validator;
use Captcha;
use App\Http\Controllers\Controller;


class UserValidatorController extends Controller
{
    public static function validateEmailLogin($email, $pwd, $code)
    {
//        if (empty($code)) {
//            return ['error' => 95, 'desc' => '验证码不能为空'];
//        }
//        if (!Captcha::check($code)) {
//            return ['error' => 96, 'desc' => '验证码错误'];
//        }
        $validator = Validator::make(
            [
                'email' => $email,
                'pwd' => $pwd
            ],
            [
                'email' => 'required|email',
                'pwd' => 'required'//|min:6'
            ],
            [
                'required' => ':attribute不能为空',
                'email' => ':attribute格式错误',
//                'min' => ':attribute最小长度为6'
            ],
            [
                'email' => '邮箱',
                'pwd' => '密码'
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

    public static function validateEmail($email)
    {
        $validator = Validator::make(
            [
                'email' => $email
            ],
            [
                'email' => 'required|email'
            ],
            [
                'required' => ':attribute不能为空',
                'email' => ':attribute格式错误'
            ],
            [
                'email' => '邮箱'
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

    public static function validateResetPwd($email, $pwd, $rePwd, $code)
    {
        $validator = Validator::make(
            [
                'email' => $email,
                'code' => $code,
                'pwd' => $pwd,
                'pwd_confirmation' => $rePwd
            ],
            [
                'email' => 'required|email',
                'code' => 'required|numeric',
                'pwd_confirmation' => 'required',
                'pwd' => 'required|confirmed'
            ],
            [
                'required' => ':attribute不能为空',
                'email' => ':attribute格式错误',
                'numeric' => ':attribute错误',
                'confirmed' => ':attribute不一样'
            ],
            [
                'email' => '邮箱',
                'code' => '验证码',
                'pwd' => '重复输入密码',
                'pwd_confirmation' => '密码'
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

    public static function validateBothPwd($pwd,$rePwd){
        $validator = Validator::make(
            [
                'pwd' => $pwd,
                'pwd_confirmation' => $rePwd
            ],
            [
                'pwd_confirmation' => 'required',
                'pwd' => 'required|confirmed'
            ],
            [
                'required' => ':attribute不能为空',
                'confirmed' => ':attribute不一样'
            ],
            [
                'pwd' => '重复输入密码',
                'pwd_confirmation' => '密码'
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

    public static function validateCode($code){
        $validator = Validator::make(
            [
                'code' => $code
            ],
            [
                'code' => 'required|size:6'
            ],
            [
                'required' => ':attribute不能为空',
                'size'=>':attribute错误'
            ],
            [
                'code' => '验证码'
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

    public static function validateAddUser($email, $name, $pwd, $rePwd){
        $validator = Validator::make(
            [
                'email' => $email,
                'name' => $name,
                'pwd' => $pwd,
                'pwd_confirmation' => $rePwd
            ],
            [
                'email' => 'required|email',
                'name' => 'required|between:2,16',
                'pwd_confirmation' => 'required',
                'pwd' => 'required|confirmed'
            ],
            [
                'required' => ':attribute不能为空',
                'email' => ':attribute格式错误',
                'between' => ':attribute长度在2-16位之间',
                'confirmed' => ':attribute不一样'
            ],
            [
                'email' => '邮箱',
                'name' => '用户名',
                'pwd' => '重复输入密码',
                'pwd_confirmation' => '密码'
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