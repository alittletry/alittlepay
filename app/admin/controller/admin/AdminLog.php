<?php


namespace app\admin\controller\admin;


use app\admin\controller\AuthController;
use app\admin\model\admin\AdminLog as lModel;
use app\Request;
use learn\services\UtilService as Util;

/**
 * 日志
 * Class AdminLog
 * @package app\admin\controller\admin
 */
class AdminLog extends AuthController
{
    protected $noNeedLogin = ['index'];

    /**
     * 分页
     * @param Request $request
     * @return string
     * @throws \think\db\exception\DbException
     */
    public function index()
    {
        $where = Util::postMore([
            ['name',''],
            ['ip',''],
            ['start_time',''],
            ['end_time',''],
        ]);
        $this->assign("where",$where);
        $this->assign("list",lModel::systemPage($where)->appends($where));
        return $this->fetch();
    }

    /**
     * 清空日志
     * @param Request $request
     * @throws \Exception
     */
    public function empty(Request $request)
    {
        lModel::where("1=1")->delete();
    }
}