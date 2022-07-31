<?php


namespace app\admin\model\project;


use app\admin\model\admin\Admin;
use app\admin\model\BaseModel;
use app\admin\model\ModelTrait;

/**
 * 项目管理
 * Class project
 * @package app\admin\model\project
 */
class project extends BaseModel
{
    use ModelTrait;

    /**
     * 列表
     * @param $where
     * @return array
     */
    public static function lst($where)
    {
        $model = new self;
        $count = self::counts($model);
        $model = $model->page((int)$where['page'],(int)$where['limit']);
        $data = $model->select()->each(function ($item)
        {
            $info = Admin::getAdminInfoById($item['manager']);
            if ($info) $item['manager'] = $info['realname'];
        });
        if ($data) $data = $data->toArray();
        return compact("data","count");
    }
}