<?php


namespace app\admin\controller\payment;

use app\admin\controller\AuthController;

use think\facade\Request as RequestAc;
use app\admin\model\payment\Payment;
use app\admin\model\order\Order;
use app\Request;
use learn\services\UtilService as Util;
use FormBuilder\Factory\Elm;
use learn\services\FormBuilderService as Form;
use think\facade\Route as Url;

/**
 * 通道管理
 * Class Index
 * @package app\admin\controller\payment
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
            ['name',''],
            ['type',''],
            ['status',''],
            ['page',1],
            ['limit',20],
        ]);
        return app("json")->layui(Payment::systemPage($where));
    }
    
    /**
     * 添加通道
     * @param Request $request
     * @return string
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function add(Request $request)
    {
        $form = array();
        $form[] = Elm::input('name','通道名称')->col(18);
         $form[] = Elm::input('image','二维码链接')->placeholder('微信输入 wxp:// 开头的，支付宝请输入userid')->col(18);
         $form[] = Elm::select('type','通道类型')->options(function(){
            $menus=[ ['value'=>'alipay','label'=>'支付宝'], ['value'=>'wxpay','label'=>'微信']];
            return $menus;
        })->col(18);
        $form[] = Elm::number('limit','每日限额')->min(0)->controls(false)->col(24);
        $form[] = Elm::radio('float_type','浮动类型',1)->options([['label'=>'上下浮动','value'=>1],['label'=>'向上浮动','value'=>2],['label'=>'向下浮动','value'=>3]])->col(18);
        $form[] = Elm::number('float_quantity','浮动次数')->min(0)->controls(false)->col(24);
        $form[] = Elm::number('float_unit','浮动单位')->min(0)->precision(2)->controls(false)->col(24);
        $form[] = Elm::radio('status','状态',1)->options([['label'=>'启用','value'=>1],['label'=>'禁用','value'=>2],['label'=>'限额','value'=>3]])->col(18);
        $form[] = Elm::radio('rotation','轮训',1)->options([['label'=>'参与','value'=>1],['label'=>'不参与','value'=>2]])->col(18);
        $form = Form::make_post_form($form, url('save')->build());
        $this->assign(compact('form'));
        return $this->fetch("public/form-builder");
    }
    /**
     * 修改通道
     * @return string
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function edit($id="")
    {
        if (!$id) return app("json")->fail("通道id不能为空");
        $ainfo = Payment::get($id);
        if (!$ainfo) return app("json")->fail("没有该通道");
        $form = array();
        $form[] = Elm::input('name','通道名称',$ainfo['name'])->col(18);
        
          $form[] = Elm::input('image','二维码链接',$ainfo['image'])->placeholder('微信输入 wxp:// 开头的，支付宝请输入userid')->col(18);
        $form[] = Elm::select('type','通道类型',$ainfo['type'])->options(function(){
            $menus=[ ['value'=>'alipay','label'=>'支付宝'], ['value'=>'wxpay','label'=>'微信']];
            return $menus;
        })->col(18);
        $form[] = Elm::number('limit','每日限额',$ainfo['limit'])->min(0)->controls(false)->col(24);
        $form[] = Elm::radio('float_type','浮动类型',$ainfo['float_type'])->options([['label'=>'上下浮动','value'=>1],['label'=>'向上浮动','value'=>2],['label'=>'向下浮动','value'=>3]])->col(18);
        $form[] = Elm::number('float_quantity','浮动次数',$ainfo['float_quantity'])->min(0)->controls(false)->col(24);
        $form[] = Elm::number('float_unit','浮动单位',$ainfo['float_unit'])->min(0)->precision(2)->controls(false)->col(24);
        $form[] = Elm::radio('status','状态',$ainfo['status'])->options([['label'=>'启用','value'=>1],['label'=>'禁用','value'=>2],['label'=>'限额','value'=>3]])->col(18);
        $form[] = Elm::radio('rotation','轮训',$ainfo['rotation'])->options([['label'=>'参与','value'=>1],['label'=>'不参与','value'=>2]])->col(18);
        $form = Form::make_post_form($form, url('save',['id'=>$id])->build());
        $this->assign(compact('form'));
        return $this->fetch("public/form-builder");
    }
    /**
     * 测试通道
     * @return string
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function test(Request $request)
    {
        $id=$request->param("id",0);
        $payment = Payment::get($id);
        $money = number_format($request->param("money",1), 2, '.', ' ');

        $order= new Order;
        $order->type = $payment['type'];
        $order->out_trade_no =  'test'.date("YmdHis").'0000'.rand(100000,999999);
        $order->notify_url = $request->domain().'/index/notify';
        $order->return_url = $request->domain().'/index/notify';
        $order->name = '测试';
        $order->money =$money;
        $order->param = '测试';
        $order->sign ='测试';
        $order->sign_type = 'MD5';
        $order->trade_no = 'test'.date("YmdHis").'0000'.rand(100000,999999);
        $order->trade_status = 'TRADE_FAIL';
        $order->create_time = time();
        $order->real_money = $money;
        $order->payment_id = $payment['id'];
        $order->save();
   
        return json(['code'=>200,'msg'=>'获取支付链接成功','url'=>$request->domain().'/api/pay/'.$order['trade_no']]);
  
    }
    /**
     * 保存修改
     * @param string $id
     * @return mixed
     */
    public function save($id="")
    {
        $data = Util::postMore([
            ['name',''],
            ['image',''],
            ['type',''],
            ['status',''],
            ['limit',''],
            ['rotation',''],
            ['float_type',''],
            ['float_unit',''],
            ['float_quantity',''],
        ]);
        if ($data['name'] == "") return app("json")->fail("通道名称不能为空");
        if ($data['image'] == "") return app("json")->fail("收款二维码不能为空");
        if ($data['type'] == "") return app("json")->fail("通道类型不能为空");
        if ($data['status'] == "") return app("json")->fail("通道状态不能为空");
        if ($data['limit'] == "") return app("json")->fail("每日限额不能为空");
        if ($data['rotation'] == "") return app("json")->fail("轮训状态不能为空");
        if (is_array($data['image'])) $data['image'] = $data['image'][0];
        if ($id=="")
        {
            $data['create_time'] = time();
            $res = Payment::insert($data);
        }else
        {
            $ainfo = Payment::get($id);
            $data['update_time'] = time();
            $res = Payment::update($data,['id'=>$id]);
        }
        return $res ? app("json")->success("操作成功",'code') : app("json")->fail("操作失败");
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
        return Payment::where('id',"in",$ids)->delete() ? app("json")->success("操作成功") : app("json")->fail("操作失败");
    }
    
    /**
     * 启用
     * @param Request $request
     * @return mixed
     */
    public function enabled(Request $request)
    {
        $ids = $request->param("id",0);
        if (empty($ids) || !$ids) return app("json")->fail("参数有误，Id为空！");
        if (!is_array($ids)) $ids = explode(",",$ids);
        return Payment::where('id',"in",$ids)->update(['status'=>1]) ? app("json")->success("操作成功") : app("json")->fail("操作失败");
    }

    /**
     * 禁用
     * @param Request $request
     * @return mixed
     */
    public function disabled(Request $request)
    {
        $ids = $request->param("id",0);
        if (empty($ids) || !$ids) return app("json")->fail("参数有误，Id为空！");
        if (!is_array($ids)) $ids = explode(",",$ids);
        return Payment::where('id',"in",$ids)->update(['status'=>2]) ? app("json")->success("操作成功") : app("json")->fail("操作失败");
    }
}