<?php

namespace App\Http\Controllers\Auth;

//use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Validator\ValidatorController;

use App\Model\UserModel;
use App\Model\RoleModel;
use App\Model\PermissionModel;

use Request;
use Session;

class TerminalController extends Controller
{
    public function loginByEmail(){
        return view('admin.auth.login');
    }

    //登录
    public function login(){
        $email = Request::input('email');
        $pwd = Request::input('pwd');
        $code = Request::input('code');
        $message = ValidatorController::validateEmailLogin($email,$pwd,$code);
        if($message!==true){
            return view('admin.auth.login')->with($message);
        }
        $email = strtolower($email);
        $pwd = md5($pwd);
        $user = UserModel::where('email',$email)->where('pwd',$pwd)->first();
        if($user){
            Session::put('userId',$user['id']);
            return redirect()->route('admin.user.index');
        }else {
            $result = ['error'=>1,'desc'=>'邮箱或密码错误'];
            return view('admin.auth.login')->with($result);
        }
    }

    //登录后的页面
    public function show(){
        if(Session::has('menus')){
            $menus = json_decode(Session::get('menus'));
//            dd(json_decode($menus));
            return view('admin.index.home')->withMenus($menus);
        }
        return 'no';
    }
}
