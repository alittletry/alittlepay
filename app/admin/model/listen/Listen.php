<?php


namespace app\admin\model\listen;

use app\admin\model\BaseModel;
use app\admin\model\ModelTrait;
use app\admin\model\order\Order;
use think\facade\Cache;
use think\facade\Session;

/**
 * 支付通道管理
 * Class Listen
 * @package app\listen\model
 */
class Listen extends BaseModel
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
        if ($where['device'] != '') $model = $model->where("device","like","%$where[device]%");
        if ($where['pkg'] != '') $model = $model->where("pkg","like","%$where[pkg]%");
        if ($where['title'] != '') $model = $model->where("title","like","%$where[title]%");
        if ($where['payment_id'] != '') $model = $model->where("payment_id",$where['payment_id']);
        if ($where['start_time'] != '') $model = $model->where("create_time",">",strtotime($where['start_time']." 00:00:00"));
        if ($where['end_time'] != '') $model = $model->where("create_time","<", strtotime($where['end_time']." 23:59:59"));
        $count = $model->count();
        $model = $model->order('create_time desc');
        if ($where['page'] && $where['limit']) $model = $model->page((int)$where['page'],(int)$where['limit']);
         
        //$count = self::counts($model);
        $data = $model->select();
        $data = $data ? $data->toArray() : [];
        return compact("data","count");
    }

}
