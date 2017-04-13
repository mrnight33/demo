<?php

use Request as Input;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('admin/login', 'Auth\UserController@loginByEmail');//->name('admin.login');
Route::post('admin/login', 'Auth\UserController@login');
Route::post('admin/addNew','Auth\UserController@addNewUser');
Route::post('admin/delete','Auth\UserController@deleteUser');
Route::post('admin/forbidden','Auth\UserController@forbiddenUser');
Route::post('admin/getEmail','Auth\UserController@getEmailCode');
Route::post('admin/checkCode','Auth\UserController@checkCode');
Route::post('admin/resetPwd','Auth\UserController@resetPwd');
Route::post('admin/updatePwd','Auth\UserController@updatePwd');
Route::post('admin/logout','Auth\UserController@logout');

Route::post('cross/addNew','Auth\InletOutletController@addNewInletAndOutlet');
Route::get('cross/addNew','Auth\InletOutletController@addNewCross');
Route::post('cross/getInfo','Auth\InletOutletController@getInletAndOutletInfo');
Route::get('cross/index','Auth\InletOutletController@index');
Route::post('cross/update','Auth\InletOutletController@updateInletAndOutlet');
Route::post('cross/updateTime','Auth\InletOutletController@setOpenAndCloseTime');
Route::post('cross/setStatus','Auth\InletOutletController@setStatus');

//Route::get('admin/user', 'Auth\TerminalController@show')->name('admin.user.index');
//
// 认证路由...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');
// 注册路由...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

Route::get('/test',function(){
    return view('welcome');
});
//验证码
Route::any('captcha-test', function()
{
    if (Request::getMethod() == 'POST')
    {
        $rules = ['captcha' => 'required|captcha'];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails())
        {
            echo '<p style="color: #ff0000;">Incorrect!</p>';
        }
        else
        {
            echo '<p style="color: #00ff30;">Matched :)</p>';
        }
    }

    $form = '<form method="post" action="captcha-test">';
    $form .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    $form .= '<p>' . captcha_img() . '</p>';
    $form .= '<p><input type="text" name="captcha"></p>';
    $form .= '<p><button type="submit" name="check">Check</button></p>';
    $form .= '</form>';
    return $form;
});
Route::any('test', function()
{
    $form = '<form method="post" action="localhost:4000/test2">';
//    $form .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    $form .= '<p><input type="password" name="password" /></p>';
    $form .= '<p><input type="password" name="password_confirmation" /></p>';
    $form .= '<p><button type="submit" name="check">Check</button></p>';
    $form .= '</form>';
    return $form;
});
Route::post('test2',function(){
        $rules = ['password'=>'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6'];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails())
        {
            echo '<p style="color: #ff0000;">Incorrect!</p>';
        }
        else
        {
            echo '<p style="color: #00ff30;">Matched :)</p>';
        }
});