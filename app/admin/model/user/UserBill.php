<?php


namespace app\admin\model\user;


use app\admin\model\BaseModel;
use app\admin\model\ModelTrait;

class UserBill extends BaseModel
{
    use ModelTrait;

    /**
     * 获取全部收入
     * @return float
     */
    public static function getAllEarn()
    {
        $model = new self;
        $model = $model->where("io",2);
        $model = $model->where("status",1);
        return $model->sum("cost");
    }

    /**
     * 最近半月内交易统计
     * @return array
     */
    public static function statistics()
    {
        $model = new self;
        $model = $model->where("io",2);
        $model = $model->where("add_time","between",[strtotime(date("Y-m-d 00:00:00",strtotime("-30 day"))),strtotime(date("Y-m-d 23:59:59",strtotime("-1 day")))]);
        $model = $model->field("from_unixtime(add_time, '%m-%d') as date, sum(cost) as num");
        $model = $model->group("date");
        $data = $model->select();
        if ($data) $data = $data->toArray();
        $label = array_column($data,"date");
        $data = array_column($data,"num");
        return compact("data","label");
    }
}