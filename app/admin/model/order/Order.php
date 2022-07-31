<?php


namespace app\admin\model\order;

use app\admin\model\BaseModel;
use app\admin\model\ModelTrait;
use app\admin\model\payment\Payment;
use think\facade\Cache;
use think\facade\Session;
/**
 * 订单管理
 * Class Order
 * @package app\admin\model
 */
class Order extends BaseModel
{
    use ModelTrait;
    /**
     * 列表
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function systemPage(array $where): array
    {
        $model = new self;
        if ($where['trade_no'] != '') $model = $model->where("trade_no","like","%$where[trade_no]%");
        if ($where['out_trade_no'] != '') $model = $model->where("out_trade_no","like","%$where[out_trade_no]%");
        if ($where['start_time'] != '') $model = $model->where("create_time",">",strtotime($where['start_time']." 00:00:00"));
        if ($where['end_time'] != '') $model = $model->where("create_time","<", strtotime($where['end_time']." 23:59:59"));
        if ($where['type'] != '') $model = $model->where("type",$where['type']);
        if ($where['payment_id'] != '') $model = $model->where("payment_id",$where['payment_id']);
        if ($where['trade_status'] != '') $model = $model->where("trade_status",$where['trade_status']);
        if ($where['notify_status'] != '') $model = $model->where("notify_status",$where['notify_status']);
        
        $count = $model->count();
        $model = $model->order('create_time desc');
        if ($where['page'] && $where['limit']) $model = $model->page((int)$where['page'],(int)$where['limit']);
        
        $data = $model->select()->each(function ($item){
            // 用户信息
            $item['pay_time']=date('Y-m-d H:i:s',$item['pay_time']);
            $item['payment_name'] = Payment::where(['id'=>$item['payment_id']])->value('name');
        });
        $data = $data ? $data->toArray() : [];
        return compact("data","count");
    }
    /**
     * 半个月内的订单统计
     * @return array
     */
    public static function statistics_num()
    {
        //半个月内用户注册统计
        $model = new self;
        $model = $model->where("create_time","between",[strtotime(date("Y-m-d 00:00:00",strtotime("-14 day"))),strtotime(date("Y-m-d 23:59:59",strtotime("-1 day")))]);
        $model = $model->where("trade_status","=","TRADE_SUCCESS");
        $model = $model->field("from_unixtime(create_time, '%m-%d') as date, count(*) as num");
        $model = $model->group("date");
        $data = $model->select();
        if ($data) $data = $data->toArray();
        $label = array_column($data,"date");
        $data = array_column($data,"num");
        return compact("data","label");
    }
    /**
     * 半个月内的成交额统计
     * @return array
     */
    public static function statistics_money()
    {
        //半个月内用户注册统计
        $model = new self;
        $model = $model->where("create_time","between",[strtotime(date("Y-m-d 00:00:00",strtotime("-14 day"))),strtotime(date("Y-m-d 23:59:59",strtotime("-1 day")))]);
        $model = $model->where("trade_status","=","TRADE_SUCCESS");
        $model = $model->field("from_unixtime(create_time, '%m-%d') as date, sum(real_money) as num");
        $model = $model->group("date");
        $data = $model->select();
        if ($data) $data = $data->toArray();
        $label = array_column($data,"date");
        $data = array_column($data,"num");
        return compact("data","label");
    }
}
