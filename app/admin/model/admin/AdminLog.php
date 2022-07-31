<?php


namespace app\admin\model\admin;


use app\admin\model\BaseModel;

/**
 * 操作日志
 * Class AdminLog
 * @package app\admin\model\admin
 */
class AdminLog extends BaseModel
{

    /**
     * 保存日志
     * @param array $adminInfo
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return bool
     */
    public static function saveLog(array $adminInfo, string $module, string $controller, string $action): bool
    {
        return self::create([
            'admin_id'      => $adminInfo['id'],
            'admin_name'    => $adminInfo['name'],
            'module'        => $module,
            'controller'    => $controller,
            'action'        => $action,
            'ip'            => request()->ip(),
            'create_time'   => time(),
            'user_agent'     => substr(request()->server('HTTP_USER_AGENT'), 0, 255),
        ]) ? true : false;
    }

    /**
     * 日志列表
     * @param $where
     * @return \think\Paginator
     * @throws \think\db\exception\DbException
     */
    public static function systemPage($where)
    {
        $model = new self;
        $model = $model->order("id desc");
        if ($where['name'] != '') $model = $model->where('admin_name|id',"like","%$where[name]%");
        if ($where['ip'] != '') $model = $model->where('ip',"like","%$where[ip]%");
        if ($where['start_time'] != '') $model = $model->where('create_time','>',strtotime($where['start_time']." 00:00:00"));
        if ($where['end_time'] != '') $model = $model->where('create_time','<', strtotime($where['start_time']." 23:59:59"));
        return $model->paginate(10)->each(function ($item){
            $item['name'] = AdminAuth::getNameByAction($item['module'],$item['controller'],$item['action']);
        });
    }
}