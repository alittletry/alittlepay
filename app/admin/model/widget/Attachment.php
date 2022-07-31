<?php


namespace app\admin\model\widget;


use app\admin\model\BaseModel;
use app\admin\model\ModelTrait;
use app\admin\model\system\SystemConfig;

/**
 * Class attachment
 * @package app\admin\model\widget
 */
class Attachment extends BaseModel
{
    use ModelTrait;

    /**
     * @param int $cid
     * @param string $name
     * @param string $path
     * @param string $type
     * @param string $mime
     * @param float $size
     * @param int $storage
     * @return int|string
     */
    public static function addAttachment(int $cid, string $name, string $path, string $type, string $mime, float $size, int $storage)
    {
        $data = [
            'cid' => $cid,
            'name' => $name,
            'path' => $path,
            'type' => $type,
            'mime' => $mime,
            'size' => $size,
            'storage' => $storage,
            'upload_time' => time()
        ];
        return self::insert($data);
    }

    /**
     * 分页显示
     * @param array $where
     * @return array
     */
    public static function pagination(array $where)
    {
        $model = self::where("type",$where['type']);
        if ($where['cid'] != "") $model = $model->where("cid", $where['cid']);
        $count = self::counts($model);
        $model = $model->order("id desc");
        $model = $model->field("path");
        $data = $model->page((int)$where['page'],(int)$where['limit'])->select();
        if ($data) $data = $data->toArray();
        return compact("data","count");
    }
}