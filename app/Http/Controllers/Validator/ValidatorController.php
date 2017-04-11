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


class ValidatorController extends Controller
{
    public static function validateEmailLogin($email, $pwd, $code)
    {
        if (empty($code)) {
            return ['error' => 95, 'desc' => '验证码不能为空'];
        }
        if (!Captcha::check($code)) {
            return ['error' => 96, 'desc' => '验证码错误'];
        }
        $validator = Validator::make(
            [
                'email' => $email,
                'pwd' => $pwd
            ],
            [
                'email' => 'required|email',
                'pwd' => 'required|min:6'
            ],
            [

            ]
        );
        if ($validator->fails()) {
            dd($validator);
            return ['error' => 94, 'desc' => '参数错误'];
        }
        return true;
    }

}