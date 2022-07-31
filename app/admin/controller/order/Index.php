<?php


namespace app\admin\controller\order;

use app\admin\controller\AuthController;

use app\admin\model\payment\Payment;
use app\admin\model\order\Order;
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
            ['trade_no',''],
            ['out_trade_no',''],
            ['type',''],
            ['start_time',''],
            ['end_time',''],
            ['payment_id',''],
            ['trade_status',''],
            ['notify_status',''],
            ['page',1],
            ['limit',20],
        ]);
        return app("json")->layui(Order::systemPage($where));
    }
    

    /**
     * 通知日志
     * @param Request $request
     * @return mixed
     */
    public function callback(Request $request)
    {
        if($request->isPost()){
            $where = Util::postMore([
            ['order_id',''],
            ['page',1],
            ['limit',10],
            ]);
            return app("json")->layui(Callback::systemPage($where)); 
        }else{
            $this->assign("order_id",$request->param('order_id'));
           return $this->fetch();
        }

       
    }
    /**
     * 删除操作
     * @param Request $request
     * @return mixed
     */
    public function del(Request $request)
    {
        $ids = $request->param("id",0);
        if (empty($ids) || !$ids) return app("json")->fail("参数有误，Id为空！");
        if (!is_array($ids)) $ids = explode(",",$ids);
       
        return Order::where('id',"in",$ids)->delete() ? app("json")->success("操作成功") : app("json")->fail("操作失败");
    }
    
    /**
     * 通知操作
     * @param Request $request
     * @return mixed
     */
    public function notify(Request $request)
    {
        $id = $request->param("id",0);
        $order = Order::find($id);
        (new Notify())->notify_original($order);
        return app("json")->success("操作成功");
    }
    /**
     * 补单操作
     * @param Request $request
     * @return mixed
     */
    public function notify_done(Request $request)
    {
        $id = $request->param("id",0);
        $order = Order::find($id);
        (new Notify())->notify_done($order);
        return app("json")->success("操作成功");
    }

}