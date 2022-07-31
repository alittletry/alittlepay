<?php


namespace app\admin\model;

use app\admin\model\BaseModel;
use app\admin\model\ModelTrait;
use app\admin\model\order\Order;
use think\facade\Cache;
use think\facade\Session;
/**
 * 订单管理
 * Class Order
 * @package app\admin\model
 */
class Callback extends BaseModel
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
        $model = $model->where("order_id",$where['order_id']);
        if ($where['page'] && $where['limit']) $model = $model->page((int)$where['page'],(int)$where['limit']);
        $count =  self::count();
        $data = $model->select();
        $data = $data ? $data->toArray() : [];
        return compact("data","count");
    }

}
