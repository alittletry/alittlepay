<?php
namespace app\api\controller;

use app\admin\model\payment\Payment;
use app\admin\model\order\Order;
use app\admin\model\listen\Listen as ListenModel;
use app\api\service\Notify;
use app\api\service\Email;
use think\facade\Request;
use think\facade\Cache;
use think\facade\Db;
class Listen
{


    public function heart()
    {
        if(!Request::has('key','post') || Request::post('key') !==systemConfig("listenkey")){
            return json(['code'=>201,'msg'=>'key错误']);
        }
        if(Request::has('alipay_id') && Request::post('alipay_id') && Payment::find(Request::post('alipay_id'))){
            Cache::set('listen_'.Request::post('alipay_id'), 1, 30);
        }
        if(Request::has('wxpay_id') && Request::post('wxpay_id') && Payment::find(Request::post('wxpay_id'))){
            Cache::set('listen_'.Request::post('wxpay_id'), 1, 30);
        }
        return json(['code'=>200,'msg'=>'通信正常']);
    }
    public function test()
    {
       throw new \think\exception\HttpException(401, '异常消息');
    }
    public function notify()
    {
        if(!Request::has('key','post') || Request::post('key') !==systemConfig("listenkey")){
            return json(['code'=>201,'msg'=>'key错误']);
        }
        $alipay_id=Request::post('alipay_id',NULL);
        $wxpay_id=Request::post('wxpay_id',NULL);
        $device=Request::post('device');
        $data = Request::post('notify');
        $model=Db::name('listen');
        $model->startTrans();
        try{
                
                $list=$model->where(['device'=>$device,'title'=>$data['title'],'content'=>$data['content'],'pkg'=>$data['pkg'],'create_time'=>strtotime($data['notify_time'])])->lock(true)->select();
                if(count($list) > 0){
                   // return json(['code'=>200,'msg'=>'重复监听内容']);
                }
                if($data['pkg']=="com.eg.android.AlipayGphone"){
                    $payment_id=$alipay_id;
                    $listen=true;
                }elseif($data['pkg']=="com.tencent.mm"){
                    $payment_id=$wxpay_id;
                    $listen=true;
                }else{
                    $payment_id=null;
                    $listen=false;
                }
                $listenId=$model->insertGetId(['payment_id'=>$payment_id,'device'=>$device,'title'=>$data['title'],'content'=>$data['content'],'pkg'=>$data['pkg'],'create_time'=>strtotime($data['notify_time'])]);
                $model->commit();
                //写监听log完成,开始分析通知内容
                if($listen){
                    if($data['pkg']=="com.eg.android.AlipayGphone"){
                      $isMatched = preg_match('/\d+.\d+/', $data['title'], $matches);
                      if($isMatched){
                          //(float)$matches[0]
                          $order=Order::where(['payment_id'=>$payment_id,'trade_status'=>'TRADE_FAIL','real_money'=>$matches[0]])->find();
                          if($order){
                              (new Notify())->notify_done($order);
                              ListenModel::where('id',$listenId)->update(['remarks'=>'绑定订单：'.$order['id']]);
                          }else{
                              ListenModel::where('id',$listenId)->update(['remarks'=>'未查到所属订单 '.$matches[0].'元']);
                          }
                      }
                    }elseif($data['pkg']=="com.tencent.mm"){
                      $isMatched = preg_match('/\d+.\d+/', $data['content'], $matches);
                      if($isMatched && $data['title'] =="微信支付"){
                          //(float)$matches[0]
                          $order=Order::where(['payment_id'=>$payment_id,'trade_status'=>'TRADE_FAIL','real_money'=>$matches[0]])->find();
                          if($order){
                              (new Notify())->notify_done($order);
                              ListenModel::where('id',$listenId)->update(['remarks'=>'绑定订单：'.$order['id']]);
                          }else{
                              ListenModel::where('id',$listenId)->update(['remarks'=>'未查到所属订单 '.$matches[0].'元']);
                          }
                      }
                        
                    }
                    $payment = Payment::find($payment_id);
                    $paymoney = Order::where(['payment_id'=>$payment_id,'trade_status'=>'TRADE_SUCCESS'])->whereDay('create_time')->sum('real_money');
                    if($payment['status']!==3 && (int)$paymoney >= (int)$payment['limit']){
                       Payment::where('id',$payment_id)->update(['status'=>3]);
                       if(systemConfig("mail_type") &&systemConfig("limit_notify")){
                           (new Email())->send('通道【'.$payment_id.$payment['name'].'】限额,账号限额'.$payment['limit'].'元,今日已收款'.(int)$paymoney.'元','通道【'.$payment_id.$payment['name'].'】限额通知');
                       }
                        
                    }
                }
               
               return json(['code'=>200,'msg'=>'回执正常']);
                
            }catch (\Exception $exception){
                $model->rollback();
                throw $exception;
            }
        
        
    }

}
