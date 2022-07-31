<?php


namespace app\admin\model\user;


use app\admin\model\BaseModel;
use app\admin\model\ModelTrait;

class User extends BaseModel
{
    use ModelTrait;

    /**
     * 添加用户
     * @param array $data
     * @return int|string
     */
    public static function addUser(array $data)
    {
        return self::insertGetId([
            'nickname' => $data['nickname'],
            'avatar' => $data['avatar'],
            'sex' => $data['sex'],
            'register_ip' => request()->ip(),
            'register_time' => time(),
            'register_type' => 1,
            'status'=>1,
            'level'=>1,
            'integral'=>0,
            'money'=>0,
        ]);
    }

    /**
     * 更新用户
     * @param array $data
     * @param int|string $uid 用户id
     * @param int $type 注册类型
     * @return User
     */
    public static function updateUser(array $data, int $uid, int $type = 0)
    {
        $model = new self;
        $model = $model->where("uid",$uid);
        if ($type != 0) $model = $model->where("register_type",$type);
        return $model->update([
            'nickname' => $data['nickname'],
            'avatar' => $data['avatar'],
            'sex' => $data['sex'],
        ]);
    }

    /**
     * 半个月内的用户统计
     * @return array
     */
    public static function statistics()
    {
        //半个月内用户注册统计
        $model = new self;
        $model = $model->where("register_time","between",[strtotime(date("Y-m-d 00:00:00",strtotime("-14 day"))),strtotime(date("Y-m-d 23:59:59",strtotime("-1 day")))]);
        $model = $model->field("from_unixtime(register_time, '%m-%d') as date, count(*) as num");
        $model = $model->group("date");
        $data = $model->select();
        if ($data) $data = $data->toArray();
        $label = array_column($data,"date");
        $data = array_column($data,"num");
        return compact("data","label");
    }
}