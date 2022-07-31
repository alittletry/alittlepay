<?php


namespace app\admin\model\admin;


use app\admin\model\BaseModel;
use app\admin\model\ModelTrait;

/**
 * 消息通知
 * Class AdminNotify
 * @package app\admin\model\admin
 */
class AdminNotify extends BaseModel
{
    use ModelTrait;

    /**
     * 系统分页
     * @param array $where
     * @return \think\Paginator
     * @throws \think\db\exception\DbException
     */
    public static function systemPage(array $where)
    {
        $model = new self;
        if ($where['start_time'] != "" && $where['end_time'] != "")
        {
            $model = $model->where("add_time","between",[strtotime($where['start_time']." 00:00:00"),strtotime($where['end_time']." 23:59:59")]);
        }
        if ($where['title'] != '') $model = $model->where("title|content","like","%$where[title]%");
        if ($where['is_read'] != '') $model = $model->where("is_read",$where['is_read']);
        if ($where['aid'] != '') $model = $model->where("aid",$where['aid']);
        $model = $model->order("is_read");
        $model = $model->order("add_time desc");
        return $model->paginate(10)->appends($where);
    }

    /**
     * 添加记录
     * @param array $data
     * @return int|string
     */
    public static function addLog(array $data)
    {
        return self::insert($data);
    }

    /**
     * 后台首页获取通知信息
     * @param int $num
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function pageList(int $num)
    {
        $model = new self;
        $model = $model->where("is_read",0);
        $count = self::counts($model);
        $model = $model->order("add_time desc");
        $model = $model->page(1,$num);
        $data = $model->select();
        if ($data) $data = $data->toArray();
        return compact("data","count");
    }
}