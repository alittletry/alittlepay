<?php


namespace app\admin\model\admin;


use app\admin\model\BaseModel;
use app\admin\model\ModelTrait;
use FormBuilder\Factory\Elm;

/**
 * 操作角色
 * Class AdminRole
 * @package app\admin\model\admin
 */
class AdminRole extends BaseModel
{
    use ModelTrait;

    /**
     * 获取权限
     * @param int $id
     * @return string
     */
    public static function getAuth(int $id): string
    {
        return self::where("id",$id)->value("auth") ?: '';
    }

    /**
     * 获取所有角色ids
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getAuthLst(): array
    {
        $data = self::where("status",1)->field("id,name")->select();
        return $data ? $data->toArray() : [];
    }

    /**
     * 获取角色名称
     * @param int $id
     * @return string
     */
    public static function getAuthNameById(int $id): string
    {
        return self::where("id",$id)->value("name") ?: (string)$id;
    }

    /**
     * 角色列表
     * @param int $pid
     * @param array $auth
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function systemPage(int $pid = -1): array
    {
        $model = new self;
        if ($pid != -1) $model = $model->where("pid",$pid);
        $model = $model->field(['id','name','pid','auth','rank','status']);
        $model = $model->order(["rank desc","id"]);
        $data = $model->select();
        return $data->toArray() ?: [];
    }

    /**
     * 获取选择数据
     * @param int $pid
     * @param array $auth
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function lst(int $pid = 0, array $auth = []): array
    {
        $model = new self;
        $model = $model->where("pid",$pid);
        $model = $model->field(['name','id']);
        $model = $model->order(["rank desc","id"]);
        $data = $model->select()->each(function ($item) use ($auth)
        {
            $item['children'] = self::lst($item['id'],$auth);
        });
        return $data->toArray() ?: [];
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
            if (is_array($v['children']) && !empty($v['children'])) {
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
        $list[] = ['label'=>'总后台','value'=>0];
        self::myOptions(self::lst(),$list, 1, true);
        return $list;
    }

    /**
     * 横线
     * @param int $num
     * @return string
     */
    public static function cross(int $num=0): string
    {
        $str = "";
        if ($num == 1) $str .= "|--";
        elseif ($num > 1) for($i=0;$i<$num;$i++)
            if ($i==0) $str .= "|--";
            else $str .= "--";
        return $str." ";
    }

    /**
     * 生成单个节点
     * @param $id
     * @param $title
     * @return array
     */
    public static function buildTreeData($id, $title, $children=[]): array
    {
        $tree = Elm::TreeData($id,$title);
        if (!empty($children)) $tree = $tree->children($children);
        return $tree->getOption();
    }
}