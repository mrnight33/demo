<?php

namespace App\Http\Controllers\Auth;

//use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Validator\ValidatorController;

use App\Model\UserModel;
use App\Model\RoleModel;

use Response;
use Request;
use Session;
use Captcha;

class LoginController extends Controller
{
    public function index(){
        return view('admin.auth.login');
    }

    public function login(){
        $email = Request::input('email');
        $pwd = Request::input('pwd');
        $code = Request::input('code');
        if(Captcha::check($code)){
            echo 'ok';
        }
        $message = ValidatorController::validateEmailLogin($email,$pwd,$code);
        if($message!==true){
            return Response::json($message);
        }
        return $message;
    }

}
