<?php


namespace app\admin\controller\listen;

use app\admin\controller\AuthController;

use app\admin\model\payment\Payment;
use app\admin\model\order\Order;
use app\admin\model\listen\Listen as ListenModel;
use app\admin\model\Callback;
use app\Request;
use learn\services\UtilService as Util;
use FormBuilder\Factory\Elm;
use learn\services\FormBuilderService as Form;
use think\facade\Route as Url;
use app\api\service\Notify;
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
        $this->assign("payments",Payment::select());
        return $this->fetch();
    }
    /**
     * 通道列表
     * @param Request $request
     * @return
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function lst(Request $request)
    {
        $where = Util::postMore([
            ['title',''],
            ['payment_id',''],
            ['device',''],
            ['pkg',''],
            ['start_time',''],
            ['end_time',''],
            ['page',1],
            ['limit',20],
        ]);
        return app("json")->layui(ListenModel::systemPage($where));
    }
    



}