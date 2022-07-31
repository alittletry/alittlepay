<?php


namespace app\admin\model\user;


use app\admin\model\BaseModel;

/**
 * Class UserMessage
 * @package app\admin\model\user
 */
class UserMessage extends BaseModel
{
    /**
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function systemPage($where)
    {
        $model = new self;
        if ($where['add_time'] != ["",""]) $model = $model->where("add_time","between",$where['add_time']);
        if ($where['is_read'] != '') $model = $model->where("is_read",$where['is_read']);
        if ($where['type'] != '') $model = $model->where("type",$where['type']);
        $count = self::counts($model);
        $model = $model->page($where['page'],$where['limit']);
        $data = $model->select()->each(function ($item){
            $item['add_time'] = date("Y-m-d H:i:s",$item['add_time']);
            $item['name'] = $item['uid'] == 0 ? '游客' : User::where("uid",$item['uid'])->value("nickname");
        });
        if ($data) $data = $data->toArray();
        return compact("data","count");
    }
}