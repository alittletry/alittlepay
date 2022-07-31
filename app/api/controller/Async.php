<?php
namespace app\api\controller;

use app\admin\model\payment\Payment;
use app\admin\model\order\Order;
use app\api\service\Notify;
use app\Request;


class Async
{


    //订单超时检测,建议每秒一次
    public function overtime()
    {
        Order::where('trade_status','TRADE_FAIL')->whereTime('create_time', '<=', time()-300)->update(['trade_status'=>'TRADE_OVERTIME']);
    }
    
    
    //超时订单清理,清理7天前的超时订单,建议每天凌晨业务量少的时候运行一次
    public function clean()
    {
        Order::where('trade_status','TRADE_OVERTIME')->whereTime('create_time', '<=', time()-(86400*7))->delete();
    }
    
    //收款限额自动停用通道,建议每分钟运行一次
    public function limit()
    {
        $payments = Payment::select();
        foreach ($payments as $val){
            $paymoney = Order::where(['payment_id'=>$val['id'],'trade_status'=>'TRADE_SUCCESS'])->whereDay('create_time')->sum('real_money');
            if($val['status']===1 && (int)$paymoney >= (int)$val['limit']){
                Payment::where('id',$val['id'])->update(['status'=>3]); 
            }elseif($val['status']===3 && (int)$paymoney < (int)$val['limit']){
                Payment::where('id',$val['id'])->update(['status'=>1]); 
            }
        }
       
    }

    //收款限额恢复,每天0点运行一次
    public function resets()
    {
        $payments = Payment::where('status',3)->update(['status'=>1]); 
    }
    
    //异步通知，对已完成订单进行通知，规则为从下单开始第 0秒 1分钟 3分钟 5分钟 10分钟,60分钟 通知一次，然后不再通知
    public function notify()
    {
        $map1 = [
        ['trade_status', '=', 'TRADE_SUCCESS'],
        ['notify_status', '=', 2],
        ['notify_count', '=', 1],
        ['pay_time', '<', time()-60]
        ];
        $map2 = [
        ['trade_status', '=', 'TRADE_SUCCESS'],
        ['notify_status', '=', 2],
        ['notify_count', '=', 2],
        ['pay_time', '<', time()-180]
        ];
        $map3 = [
        ['trade_status', '=', 'TRADE_SUCCESS'],
        ['notify_status', '=', 2],
        ['notify_count', '=', 3],
        ['pay_time', '<', time()-300]
        ];
        $map4 = [
        ['trade_status', '=', 'TRADE_SUCCESS'],
        ['notify_status', '=', 2],
        ['notify_count', '=', 4],
        ['pay_time', '<', time()-600]
        ];
        $map5 = [
        ['trade_status', '=', 'TRADE_SUCCESS'],
        ['notify_status', '=', 2],
        ['notify_count', '=', 5],
        ['pay_time', '<', time()-3600]
        ];
        $orders = Order::whereOr([$map1,$map2,$map3,$map4,$map5])->order('notify_count asc')->select();
        foreach ($orders as $val){
            (new Notify())->notify_original($order);
        }
        
    }
    

}
