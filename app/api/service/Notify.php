<?php
namespace app\api\service;

use app\admin\model\payment\Payment;
use app\admin\model\order\Order;
use app\admin\model\Callback;
use app\api\service\Sign;

class Notify
{
    public function notify_original($order)
    {
        $this->http_post($order);
        
    }
    public function notify_done($order)
    {
        Order::where('id',$order['id'])->update(['trade_status'=>'TRADE_SUCCESS','pay_time'=>time()]);
        $order['trade_status']='TRADE_SUCCESS';
        $this->http_post($order);
        
    }
    public function http_post($order){
        
        $data =  (new Sign())->create($order);
           //初使化init方法
           $ch = curl_init();
           //指定URL
           curl_setopt($ch, CURLOPT_URL, $order['notify_url']);
           //设定请求后返回结果
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           //声明使用POST方式来进行发送
           curl_setopt($ch, CURLOPT_POST, 1);
           //发送什么数据呢
           curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
           //忽略证书
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
           curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
           //忽略header头信息
           curl_setopt($ch, CURLOPT_HEADER, 0);
           //设置超时时间
           curl_setopt($ch, CURLOPT_TIMEOUT, 10);
           //发送请求
           $output = curl_exec($ch);
           //关闭curl
           curl_close($ch);
           //返回数据
          
           if($output ==='SUCCESS' ||$output ==='success'|| strstr($output,'success')){
               Order::where('id',$order['id'])->inc('notify_count')->update(['notify_status'=>1]);
           }else{
               Order::where('id',$order['id'])->inc('notify_count')->update(['notify_status'=>2]);
           }
           $callback = new Callback();
           $callback->order_id = $order['id'];
           $callback->param = json_encode($data);
           $callback->return = $output;
           $callback->save();
           
        
    }
}
