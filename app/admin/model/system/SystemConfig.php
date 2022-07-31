<?php


namespace app\admin\model\system;


use app\admin\model\BaseModel;
use app\admin\model\ModelTrait;

/**
 * 系统配置
 * Class SystemConfig
 * @package app\admin\model\system
 */
class SystemConfig extends BaseModel
{
    use ModelTrait;

    /**
     * 列表
     * @param int $tab_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function lst($where): array
    {
        $model = new self;
        if ($where['tab_id']) $model = $model->where('tab_id',$where['tab_id']);
        $count = self::counts($model);
        if ($where['page'] && $where['limit']) $model = $model->page((int)$where['page'],(int)$where['limit']);
        $data = $model->select();
        if ($data) $data = $data->toArray();
        return compact('data','count');
    }

    /**
     * 获取字段值
     * @param string $formName
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getValueByFormName(string $formName): string
    {
        $model = new self;
        $model = $model->where("form_name",$formName);
        $model = $model->where("status",1);
        $info = $model->find();
        return $info ? $info['value'] : '';
    }

    /**
     * 获取字段值
     * @param string $formNames
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getValuesByFormNames(array $formNames): array
    {
        $model = new self;
        $model = $model->where("form_name",'in', $formNames);
        $model = $model->where("status",1);
        $info = $model->select();
        return $info ? $info->toArray() : [];
    }

    /**
     * 获取参数
     * @param int $tab_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getLstByTabId(int $tab_id = 0): array
    {
        $model = new self;
        $model = $model->where("tab_id",$tab_id);
        $model = $model->where("status",1);
        $model = $model->where("is_show",1);
        $model = $model->order("rank desc");
        $info = $model->select();
        return $info ? $info->toArray() : [];
    }

    /**
     * 修改value
     * @param string $form_name
     * @param string $value
     * @return SystemConfig
     */
    public static function editValueByFormName(string $form_name, string $value)
    {
        $model = new self;
        $model = $model->where("form_name", $form_name);
        return $model->update(['value'=>$value]);
    }
}