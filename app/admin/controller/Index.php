<?php


namespace app\admin\controller;

use app\admin\model\admin\AdminAuth;
use app\admin\model\admin\AdminNotify;
use app\admin\model\payment\Payment;
use app\admin\model\order\Order;
use app\admin\model\project\project;
use app\admin\model\user\User;
use app\admin\model\user\UserBill;
use app\Request;
use learn\services\UtilService as Util;

class Index extends AuthController
{
    // 无需登录的
    protected $noNeedLogin = [''];
    // 无需权限的
    protected $noNeedRight = [''];

    /**
     * 后台首页
     * @return string
     * @throws \Exception
     */
    public function index()
    {
        $this->assign("adminInfo",$this->adminInfo);
        $this->assign("menu",AdminAuth::getMenu(0,$this->auth));
        $this->assign("message",AdminNotify::pageList(5));
        return $this->fetch();
    }

    /**
     * 控制台
     * @return string
     * @throws \Exception
     */
    public function main()
    {
        $where = Util::postMore([
            ['page',1],
            ['limit',20],
        ]);
        $this->assign("todayMoney",number_format(Order::where(['trade_status'=>'TRADE_SUCCESS'])->whereDay('create_time')->sum('real_money'),2));
        $this->assign("todayOrder",Order::whereDay('create_time')->count()); 
        $this->assign("successAlpay",Payment::where(['type'=>'alipay','status'=>1])->count()); 
        $this->assign("successWxpay",Payment::where(['type'=>'wxpay','status'=>1])->count()); 
        $this->assign("allMoney",number_format(Order::where(['trade_status'=>'TRADE_SUCCESS'])->sum('real_money'),2));
        
        $this->assign("order",Order::statistics_num()); // 最近14天交易金额
        $this->assign("money",Order::statistics_money()); // 最近14天交易金额
        return $this->fetch();
    }

    /**
     * 菜单
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function menu()
    {
        return app("json")->success(AdminAuth::getMenu(0,$this->auth));
    }

    /**
     * @param Request $request
     * @return
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function clearCache(Request $request)
    {
        $adminPath = config("cache.runtime")."/admin/";
        $indexPath = config("cache.runtime")."/index/";
        $apiPath = config("cache.runtime")."/api/";
        if (removeCache($adminPath) && removeCache($indexPath) && removeCache($apiPath)) return app("json")->success("操作成功");
        return app("json")->fail("操作失败");
    }
}
