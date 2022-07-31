<?php


namespace app\admin\model\payment;

use app\admin\model\BaseModel;
use app\admin\model\ModelTrait;
use app\admin\model\order\Order;
use think\facade\Cache;
use think\facade\Session;

/**
 * 支付通道管理
 * Class Payment
 * @package app\payment\model
 */
class Payment extends BaseModel
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
        if ($where['name'] != '') $model = $model->where("name","like","%$where[name]%");
        if ($where['type'] != '') $model = $model->where("type",$where['type']);
        if ($where['status'] != '') $model = $model->where("status",$where['status']);
        $count = $model->count();
        if ($where['page'] && $where['limit']) $model = $model->page((int)$where['page'],(int)$where['limit']);
      //  $count =  self::count();
        $data = $model->select()->each(function ($item){
            // 用户信息
            $item['listen_status']=Cache::get('listen_'.$item['id']);
            $item['limit']=number_format($item['limit'],2);
            $item['today'] = number_format(Order::where(['payment_id'=>$item['id'],'trade_status'=>'TRADE_SUCCESS'])->whereDay('create_time')->sum('real_money'),2);
        });
        $data = $data ? $data->toArray() : [];
        return compact("data","count");
    }

}
