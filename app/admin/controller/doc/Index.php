<?php


namespace app\admin\controller\doc;

use app\admin\controller\AuthController;

use think\facade\Request;

/**
 * 订单管理
 * Class Index
 * @package app\admin\controller\order
 */
class Index extends AuthController
{
    /**
     * 通道列表
     * @return string
     * @throws \Exception
     */
    public function index()
    {
        $this->assign("domain",Request::domain());
        return $this->fetch();
    }




}