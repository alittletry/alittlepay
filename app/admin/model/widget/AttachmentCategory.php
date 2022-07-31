<?php


namespace app\admin\model\widget;


use app\admin\model\BaseModel;
use app\admin\model\ModelTrait;

/**
 * Class AttachmentCategory
 * @package app\admin\model\widget
 */
class AttachmentCategory extends BaseModel
{
    use ModelTrait;

    /**
     * 获取目录
     * @param string $type
     * @param int $pid
     * @param bool $lower
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getCategoryLst(string $type = "image", int $pid = 0, bool $lower = false)
    {
        $model = new self;
        $model = $model->where("type",$type);
        $model = $model->where("pid",$pid);
        $model = $model->field(['id','pid','name']);
        $data = $model->select();
        if ($lower)
        {
            $data = $data->each(function ($item) use ($type){
                $item['children'] = self::getCategoryLst($type, $item['id']);
            });
        }
        return $data ? $data->toArray() : [];
    }

    /**
     * 获取目录名称
     * @param int $id
     * @return string
     */
    public static function getNameById(int $id)
    {
        return self::where("id",$id)->value("name") ?: "";
    }

    /**
     * @param string $type
     * @param int $pid
     * @param string $title
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function buildNodes(string $type = "image", int $pid = 0, string $title = "")
    {
        $model = new self;
        $model = $model->where("type",$type);
        $model = $model->where("pid",$pid);
        if ($title != "") $model = $model->where("name","like","%$title%");
        $model = $model->field(['id','pid','name as text']);
        $data = $model->select()->each(function ($item) use ($type){
            $item['nodes'] = self::buildNodes($type, $item['id']);
            if (empty($item['nodes'])) unset($item['nodes']);
        });
        return $data ? $data->toArray() : [];
    }

    /**
     * 遍历选择项
     * @param array $data
     * @param $list
     * @param int $num
     * @param bool $clear
     */
    public static function myOptions(array $data, &$list, $num = 0, $clear=true)
    {
        foreach ($data as $k=>$v)
        {
            $list[] = ['value'=>$v['id'],'label'=>self::cross($num).$v['name']];
            if (isset($v['children']) && is_array($v['children']) && !empty($v['children'])) {
                self::myOptions($v['children'],$list,$num+1,false);
            }
        }
    }

    /**
     * 返回选择项
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function returnOptions(): array
    {
        $list = [];
        self::myOptions(self::getCategoryLst('image','0',true),$list, 0, true);
        return $list;
    }
}