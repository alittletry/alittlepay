<?php


namespace app\admin\controller\admin;


use app\admin\controller\AuthController;
use learn\services\UtilService as Util;
use app\admin\model\admin\AdminNotify as nModel;
/**
 * 消息提醒
 * Class AdminNotify
 * @package app\admin\controller\admin
 */
class AdminNotify extends AuthController
{
    /**
     * 列表
     * @return mixed
     * @throws \Exception
     */
    public function index()
    {
        $where = Util::postMore([
            ['title',''],
            ['start_time',''],
            ['end_time',''],
            ['is_read',''],
            ['aid',$this->adminId],
        ]);
        $this->assign("where",$where);
        $this->assign("list",nModel::systemPage($where));
        return $this->fetch();
    }
}