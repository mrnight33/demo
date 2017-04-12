<?php

namespace App\Http\Controllers\Auth;

//use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Validator\UserValidatorController;

use App\Model\UserModel;
use App\Model\RoleModel;
use App\Model\PermissionModel;
use App\Model\UserRoleModel;

use Request;
use Session;
use Log;
use DB;
use Mail;

class UserController extends Controller
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
            return $message;
            //return view('admin.auth.login')->with($message);
        }
        $email = strtolower($email);
        $pwd = md5($pwd);
        $user = UserModel::where('email',$email)->where('pwd',$pwd)->first();
        if($user){
            if($user['disabled']===0) {
                Session::put('userId', $user['id']);
                Session::put('name', $user['name']);
                $result = ['error'=>0,'desc'=>'登录成功'];
                //return redirect()->route('admin.user.index');
            }else{
                $result = ['error'=>2,'desc'=>'当前用户已被禁止登录'];
            }
        }else {
            $result = ['error'=>1,'desc'=>'邮箱或密码错误'];
        }
        return $result;//view('admin.auth.login')->with($result);
    }

    //禁用用户
    public function forbiddenUser(){
        $userId = Session::get('userId');
        $name = Session::get('name');
        $id = Request::input('id');
        if(isset($userId)&&isset($id)&&is_numeric($id)){
            if($id===$userId){
                return ['error'=>3,'desc'=>'禁止禁用当前登录用户'];
            }
            $user = UserModel::find($id);
            if($user['disabled']===0){
                Log::info('用户'.$name.'(id:'.$userId.'禁用用户'.$id.')');
                $user['disabled']=1;
                $user->save();
                $result =  ['error'=>0,'desc'=>'已禁用该用户'];
            }else {
                $result =  ['error' => 1, 'desc' => '该用户已被禁用'];
            }
        }else {
            $result =  ['error' => 2, 'desc' => '参数错误，请重试'];
        }
        return $result;
    }
    //删除用户
    public function deleteUser(){
        $userId = Session::get('userId');
        $name = Session::get('name');
        $id = Request::input('id');
        if(isset($userId)&&isset($id)&&is_numeric($id)){
            if($id===$userId){
                return ['error'=>3,'desc'=>'禁止删除当前登录用户'];
            }
            $user = UserModel::find($id);
            if($user){
                Log::info('用户'.$name.'(id:'.$userId.')删除用户id='.$id);
                try {
                    DB::beginTransaction();
                    $user_role = UserRoleModel::where('user_id',$id)->first();
                    if(isset($user_role)) {
                        UserRoleModel::where('user_id',$id)->delete();
                    }
                    $user->delete();
                    DB::commit();
                    return ['error' => 0, 'desc' => '已删除该用户'];
                }catch (\Exception $e){
                    DB::rollBack();
                    Log::error('用户'.$name.'(id:'.$userId.')删除用户'.$id.'错误.error='.$e);
                    $result = ['error'=>4,'desc'=>'删除该用户出错'];
                }
            }else {
                $result =  ['error' => 1, 'desc' => '该用户不存在'];
            }
        }else {
            $result = ['error' => 2, 'desc' => '参数错误，请重试'];
        }
        return $result;
    }

    //修改密码和重置密码流程:通过邮箱获取验证码->输入验证码->输入两次密码
    //获取邮箱验证码,跳转到输入验证码,页面输入retakeMethod区分重置和修改，email
    public function getEmailCode(){
        $email = Request::input('email');
        $message = ValidatorController::validateEmail($email);
        if($message!==true){
            return $message;
        }
        $method = Request::get('retakeMethod')?:'update';
        if($method=='update') {
            $subject = '修改密码';
        }else{
            $subject = '重置密码';
        }
        $email = strtolower($email);
        $user = UserModel::where('email',$email)->first();
        if(!$user){
            return ['error'=>2,'desc'=>'用户不存在'];
        }
        $code = rand(100000,999999);//随机6位数
        $time = time();
        $data = ['email'=>$email,'code'=>$code,'subject'=>$subject];
        $flag = Mail::send('admin.auth.email', $data, function ($message) use($data){
            $message->to($data['email']);
            $message->subject($data['subject']);
        });
        if($flag){
            Log::info('用户邮箱'.$email.'发送验证码邮件成功');
            Request::session()->put('email', $email);//邮箱放入Session不能再修改
            Request::session()->put('checkCode', $code);
            Request::session()->put('checkTime', $time);
            return json_encode(['error'=>0,'desc'=>'验证码已成功发送该邮箱','method'=>$method,'path'=>'admin.auth.checkCode']);
        }else{
            Log::error('用户邮箱'.$email.'发送验证码邮件失败');
            return json_encode(['error'=>1,'desc'=>'验证码发送失败']);
        }
    }

    //验证邮箱验证码,跳转到输入密码,页面输入retakeMethod区分重置和修改
    public function checkCode(){
        $code = Request::input('code');
        $message = ValidatorController::validateCode($code);
        if($message!==true){
            return $message;
        }
        $method = Request::get('retakeMethod')?:'update';
        $time = time();
        $email = Session::has('email')?Session::get('email'):null;
        $checkCode = Session::has('checkCode')?Session::get('checkCode'):null;
        $checkTime = Session::has('checkTime')?Session::get('checkTime'):null;
        $flag = empty($checkTime)||$time>$checkTime+30*60||$checkTime>$time;
        $status = empty($email)||empty($checkCode)||$checkCode!=$code;
        if($flag||$status){
            return ['error'=>1,'desc'=>'验证码无效'];
        }
        //通过清初Session
        Session::forget(['checkCode','checkTime']);
        return ['error'=>0,'desc'=>'验证码正确','method'=>$method,'path'=>'admin.auth.password'];
    }
    //重置密码,页面输入retakeMethod区分重置和修改
    public function resetPwd(){
        $pwd = Request::input('pwd');
        $rePwd = Request::input('rePwd');
        $message = ValidatorController::validateBothPwd($pwd,$rePwd);
        if($message!==true){
            return $message;
        }
        $email = Session::get('email')?:null;
        $method = Request::input('retakeMethod')?:null;
        $pwd = md5($pwd);
        if($email&&$method=='reset'){
            try{
                Log::info('用户邮箱'.$email.'重置密码');
                DB::beginTransaction();
                UserModel::where('email',$email)->update([
                    'pwd'=>$pwd
                ]);
                DB::commit();
                $result = ['error'=>0,'desc'=>'重置密码成功'];
            }catch (\Exception $e){
                DB::rollBack();
                Log::error('用户邮箱'.$email.'重置密码失败.error'.$e);
                $result =  ['error'=>1,'desc'=>'重置密码失败'];
            }
        }else{
            $result = ['error'=>2,'desc'=>'参数错误，请重试'];
        }
        return $result;
    }


    //修改密码，页面输入retakeMethod区分重置和修改
    public function updatePwd(){
        $userId = Session::get('userId');
        $name = Session::get('name');
        $email = Session::get('email');
        $pwd = Request::input('pwd');
        $rePwd = Request::input('rePwd');
        $message = ValidatorController::validateBothPwd($pwd,$rePwd);
        if($message!==true){
            return $message;
        }
        $pwd = md5($pwd);
        $method = Request::get('retakeMethod')?:null;
        if(isset($userId)&&isset($name)&&$method=='update'){
            $user = UserModel::where('email',$email)->first();//find($userId);
            if($user) {
                try {
                    Log::info('用户' . $name . '(id:' . $userId . ')修改密码');
                    DB::beginTransaction();
                    UserModel::where('id',$userId)->where('email',$email)->update([
                        'pwd' => $pwd
                    ]);
                    DB::commit();
                    $result = ['error' => 0, 'desc' => '修改密码成功'];
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('' . $name . '(id:' . $userId . ')修改密码失败.error' . $e);
                    $result = ['error' => 1, 'desc' => '修改密码失败'];
                }
            }else{
                $result = ['error'=>3,'用户信息不匹配，请重试'];
            }
        }else{
            $result = ['error'=>2,'desc'=>'参数错误，请重试'];
        }
        return $result;
    }

    //新建用户
    public function addNewUser(){
        $userId = Session::get('userId');
        $userName = Session::get('name');
        $email = Request::input('email');
        $name = Request::input('name');
        $pwd = Request::input('pwd');
        $rePwd = Request::input('rePwd');
        $message = ValidatorController::validateAddUser($email,$name,$pwd,$rePwd);
        if($message!==true){
            return $message;
        }
        $pwd = md5($pwd);
        $email = strtolower($email);
        if(isset($userId)&&isset($userName)){
            $user = UserModel::where('email',$email)->first();
            if($user){
                $result = ['error'=>2,'desc'=>'该邮箱已存在'];
            }else {
                Log::info('用户' . $userName . '(id:' . $userId . ')新建用户user_info=' . json_encode(['email' => $email, 'name' => $name]));
                try {
                    DB::beginTransaction();
                    $id = UserModel::insertGetId([
                        'name' => $name,
                        'email' => $email,
                        'pwd' => $pwd,
                    ]);
                    $role_ids = Request::input('role_id') ?: [];
                    $role_ids = $this->toNumberArray($role_ids);
                    if (!empty($role_ids)) {
                        foreach ($role_ids as $role_id) {
                            UserRoleModel::insert([
                                'role_id' => $role_id,
                                'user_id' => $id
                            ]);
                        }
                    }
                    DB::commit();
                    $result = ['error' => 0, 'desc' => '新建用户成功'];
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('用户' . $userName . '(id:' . $userId . ')新建用户user_info=' . json_encode(['email' => $email, 'name' => $name]) . '失败.error=' . $e);
                    $result = ['error' => 1, 'desc' => '新建用户失败'];
                }
            }
        }else{
            $result = ['error'=>4,'desc'=>'参数错误，请重试'];
        }
        return $result;
    }

    public function logout(){
        Session::flush();
        Session::regenerate();
        return ['error'=>0,'desc'=>'已成功退出'];
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

    private function toNumberArray(array $strings){
        for($i=0;$i<count($strings);$i++){
            $strings[$i] = intval($strings[$i]);
        }
        return $strings;
    }
}
