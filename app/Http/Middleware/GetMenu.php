<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\UserModel;
use App\Model\RoleModel;

class GetMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $uri = $request->path();
        if(strstr($uri,'login')||strstr($uri,'getValidateCode')||strstr($uri,'test')){
            return $next($request);
        }
        echo $uri.'<br>';
        if($request->session()->has('userId')){
            $userId = $request->session()->get('userId');
            $request->session()->put('menus', $this->getMenu($userId));
            return $next($request);
        }
        return redirect('/login');
    }

    private function getMenu($id){
        $menus = [];
        $roles = UserModel::find($id)->roles()->first();
        if($roles){
            $role_id = $roles['id'];
            $permissions = RoleModel::find($role_id)->permissions()->select('id');
            if($permissions){
                foreach($permissions as $value){
                    array_push($menus,$value);
                }
            }
        }
        return $menus;
    }
}
