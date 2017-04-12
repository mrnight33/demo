<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\UserModel;
use App\Model\RoleModel;
use App\Model\PermissionModel;

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
        if(strstr($uri,'login')||strstr($uri,'test')){
            return $next($request);
        }
        echo $uri.'<br>';
        if($request->session()->has('userId')){
            $userId = $request->session()->get('userId');
            $request->session()->put('comData_menu', $this->getMenu($userId));
            return $next($request);
        }
        return redirect('admin/login');
    }

    private function getMenu($id){
//        $openArr = [];
        $data = [];
//        $data['top'] = [];
        //查找并拼接出地址的别名值
        $path_arr = explode('/', \URL::getRequest()->path());
        if (isset($path_arr[1])) {
            $urlPath = $path_arr[0] . '.' . $path_arr[1] . '.index';
        } else {
            $urlPath = $path_arr[0] . '.index';
        }
        $data['curr_url'] = $urlPath;
        $roles = UserModel::find($id)->roles()->first();
        $permissions = RoleModel::find($roles->id)->permissions()->get()->toArray();
        if($permissions) {
            $permission_ids = [];
            foreach ($permissions as $permission) {
                $id = $permission['id'];
                array_push($permission_ids, $id);
            }
            //查找出所有的地址
            $table = PermissionModel::whereIn('id',$permission_ids)->get()->toArray();

//            foreach ($table as $v) {
//                if ($v['pid'] == 0) {
//                    if ($v['url'] == $urlPath) {
//                        $openArr[] = $v['id'];
//                        $openArr[] = $v['pid'];
//                    }
//                    $data[$v['pid']][] = $v;
//                }
//            }
//            foreach ($data[0] as $v) {
//                if (isset($data[$v['id']]) && is_array($data[$v['id']]) && count($data[$v['id']]) > 0) {
//                    $data['top'][] = $v;
//                }
//            }
//            unset($data[0]);
//            //ation open 可以在函数中计算给他
//            $data['openarr'] = array_unique($openArr);
//            dd($data);
            return $data;
        }
        return null;
    }


    private function getData(array $tables){
        $menus = [];
        foreach ($tables as $table) {
            if(PermissionModel::getParent($table['pid'])) {
                array_push($menus[$table['pid']],$table);
            }
            array_push($menus['top'],$table);
        }
        return $menus;
    }
}
