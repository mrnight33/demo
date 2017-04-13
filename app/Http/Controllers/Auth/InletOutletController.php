<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Http\Controllers\validator\InletOutletValidatorController;
use App\Model\InletOutletModel;

use Request;
use Log;
use Session;
use DB;


class InletOutletController extends Controller
{

    public function index()
    {
        $sort = Request::input('sort') ?: 'id';
        $data = InletOutletModel::orderBy($sort)->get();
        return ['error' => 0, 'desc' => '获取成功', 'data' => $data];
    }

    public function addNewCross()
    {
        return view('admin.cross.add')->with(['longitude' => 106.486486, 'latitude' => 29.600933]);//29.599515,106.487831;
    }

    //新建出入口
    public function addNewInletAndOutlet()
    {
        $userName = Session::get('name');
        $userId = Session::get('userId');
        if (empty($userName) || empty($userId)) {
            return ['error' => 99, 'desc' => '未登录'];
        }
        $longitude = Request::input('longitude');
        $latitude = Request::input('latitude');
        $name = Request::input('name');
        $openTime = Request::input('open_time');
        $closeTime = Request::input('close_time');
        $status = Request::input('status') ?: 0;
        if (empty($openTime) || empty($closeTime)) {
            return ['error' => 3, 'desc' => '参数错误'];
        }
        $message = InletOutletValidatorController::validateAddNewInletAndOutlet($name, $longitude, $latitude, $openTime, $closeTime, $status);
        if ($message !== true) {
            return $message;
        }
        $inletOutlet1 = InletOutletModel::where('name', $name)->first();
        if ($inletOutlet1) {
            return ['error' => 1, 'desc' => '该名称已存在'];
        }
        $inletOutlet2 = InletOutletModel::where('longitude', $longitude)->where('latitude', $latitude)->first();
        if ($inletOutlet2) {
            $result = ['error' => 4, 'desc' => '该出入口已存在'];
        } else {
            $timeZone = $openTime . '-' . $closeTime;
            $time = $this->getUnixTimestamp($closeTime) - $this->getUnixTimestamp($openTime);
            if ($time < 0) {
                $result = ['error' => 4, 'desc' => '开放时间错误'];
            } else {
                try {
                    Log::info('用户' . $userName . '(id:' . $userId . ')新建出入口，info=' . json_encode(Request::all()));
                    DB::beginTransaction();
                    InletOutletModel::insert([
                        'name' => $name,
                        'longitude' => $longitude,
                        'latitude' => $latitude,
                        'open_time' => $openTime,
                        'time_zone' => $timeZone,
                        'time' => $time,
                        'status' => $status
                    ]);
                    DB::commit();
                    Log::info('用户' . $userName . '(id:' . $userId . ')新建出入口成功，info=' . json_encode(Request::all()));
                    $result = ['error' => 0, 'desc' => '新建出入口成功'];
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('用户' . $userName . '(id:' . $userId . ')新建出入口失败，info=' . json_encode(Request::all()) . ',error=' . $e);
                    $result = ['error' => 2, 'desc' => '新建出入口失败'];
                }
            }
        }
        return $result;
    }

    //编辑出入口
    public function updateInletAndOutlet()
    {
        $userId = Session::get('userId');
        $userName = Session::get('name');
        if (empty($userId) || empty($userName)) {
            return ['error' => 99, 'desc' => '未登录'];
        }
        $id = Request::input('id');
        $name = Request::input('name');
        $longitude = Request::input('longitude');
        $latitude = Request::input('latitude');
        $message = InletOutletValidatorController::validateUpdateInfo($id, $name, $longitude, $latitude);
        if ($message !== true) {
            return $message;
        }
        $inletOutlet = InletOutletModel::find($id);
        if ($inletOutlet) {
            try {
                Log::info('用户' . $name . '(id:' . $userId . ')编辑出入口(name:' . $inletOutlet['name'] . ')信息,info=' . json_encode(Request::all()));
                DB::beginTransaction();
                InletOutletModel::where('id', $id)->update([
                    'name' => $name,
                    'longitude' => $longitude,
                    'latitude' => $latitude
                ]);
                DB::commit();
                Log::info('用户' . $name . '(id:' . $userId . ')编辑出入口(name:' . $inletOutlet['name'] . ')信息成功');
                $result = ['error' => 0, 'desc' => '修改成功'];
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('用户' . $name . '(id:' . $userId . ')编辑出入口(name:' . $inletOutlet['name'] . ')信息失败,info=' . json_encode(Request::all()) . ',error=' . $e);
                $result = ['error' => 2, 'desc' => '编辑出入口信息失败'];
            }
        } else {
            $result = ['error' => 1, 'desc' => '该出入口不存在'];
        }
        return $result;
    }

    //设置出入口的开放关闭时间
    public function setOpenAndCloseTime()
    {
        $userId = Session::get('userId');
        $name = Session::get('name');
        if (empty($userId) || empty($name)) {
            return ['error' => 99, 'desc' => '未登录'];
        }
        $id = Request::input('id');
        $openTime = Request::input('openTime');
        $closeTime = Request::input('closeTime');
        if (empty($openTime) || empty($closeTime)) {
            return ['error' => 3, 'desc' => '参数错误'];
        }
        $message = InletOutletValidatorController::validateOpenAndCloseTime($id, $openTime, $closeTime);
        if ($message !== true) {
            return $message;
        }
        $inletOutlet = InletOutletModel::find($id);
        if ($inletOutlet) {
            $timeZone = $openTime . '-' . $closeTime;
            $time = $this->getUnixTimestamp($closeTime) - $this->getUnixTimestamp($openTime);
            if ($time < 0) {
                $result = ['error' => 4, 'desc' => '开放时间错误'];
            } else {
                try {
                    Log::info('用户' . $name . '(id:' . $userId . ')修改出入口(name:' . $inletOutlet['name'] . ')时间段,info=' . json_encode(Request::all()));
                    DB::beginTransaction();
                    InletOutletModel::where('id', $id)->update([
                        'open_time' => $openTime,
                        'time_zone' => $timeZone,
                        'time' => $time
                    ]);
                    DB::commit();
                    Log::info('用户' . $name . '(id:' . $userId . ')修改出入口(name:' . $inletOutlet['name'] . ')时间段成功');
                    $result = ['error' => 0, 'desc' => '修改成功'];
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('用户' . $name . '(id:' . $userId . ')修改出入口(name:' . $inletOutlet['name'] . ')时间段失败,info=' . json_encode(Request::all()) . ',error=' . $e);
                    $result = ['error' => 2, 'desc' => '修改出入口时间段失败'];
                }
            }
        } else {
            $result = ['error' => 1, 'desc' => '该出入口不存在'];
        }
        return $result;
    }

    //设置出入口通行状态
    public function setStatus()
    {
        $userId = Session::get('userId');
        $name = Session::get('name');
        if (empty($userId) || empty($name)) {
            return ['error' => 99, 'desc' => '未登录'];
        }
        $id = Request::input('id');
        $status = Request::input('status');
        $message = InletOutletValidatorController::validateStatus($id, $status);
        if ($message !== true) {
            return $message;
        }
        $inletOutlet = InletOutletModel::find($id);
        if ($inletOutlet) {
            try {
                Log::info('用户' . $name . '(id:' . $userId . ')设置出入口(name:' . $inletOutlet['name'] . ')通行状态,info=' . json_encode(Request::all()));
                DB::beginTransaction();
                InletOutletModel::where('id', $id)->update([
                    'status' => $status
                ]);
                DB::commit();
                Log::info('用户' . $name . '(id:' . $userId . ')设置出入口(name:' . $inletOutlet['name'] . ')通行状态成功');
                $result = ['error' => 0, 'desc' => '设置成功'];
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('用户' . $name . '(id:' . $userId . ')设置出入口(name:' . $inletOutlet['name'] . ')时通行状态失败,info=' . json_encode(Request::all()) . ',error=' . $e);
                $result = ['error' => 2, 'desc' => '设置出入口状态失败'];
            }
        } else {
            $result = ['error' => 1, 'desc' => '该出入口不存在'];
        }
        return $result;
    }

    public function getInletAndOutletInfo()
    {
        $hour = date('H');
        $minute = date('i');
        $curr_time = $hour * 60 * 60 + $minute * 60;
        $data = InletOutletModel::where(DB::raw('TIME_TO_SEC(open_time)'), '<', $curr_time)->
        where(DB::raw('TIME_TO_SEC(open_time)+time'), '>', $curr_time)->
        where('status', 0)->get()->toArray();
        $array = [];
        foreach ($data as $value) {
            $beginTime = $this->getUnixTimestamp($value['open_time']);
            $value['beginTime'] = $beginTime;
            $value['endTime'] = $beginTime + $value['time'];
            array_push($array, $value);
        }
        $result = ['error' => 0, 'desc' => '获取成功', 'info' => $array];
        return $result;
    }

    public function getUnixTimestamp($time)
    {
        $timeInfo = explode(':', $time);
        $year = date('y');
        $month = date('m');
        $day = date('d');
        $hour = $timeInfo[0];
        $minute = $timeInfo[1];
        return mktime($hour, $minute, '00', $month, $day, $year);
    }
}
