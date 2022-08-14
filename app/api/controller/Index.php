<?php
namespace app\api\controller;

use app\admin\model\payment\Payment;
use app\admin\model\order\Order;
use app\api\service\Sign;
use think\facade\Request;
use app\api\service\Email;
use think\facade\Db;
use think\facade\View;
/**
 * Class Index
 * @package app\index\controller
 */
class Index
{

    
    /**
     * 提交订单
     * @return view
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function submit()
    {
        if(!Request::has('pid'))return '请检查参数【pid】';
        if(!Request::has('type') || (Request::param('type') !=='alipay' && Request::param('type') !=='wxpay' ))return '请检查参数【type】';  
        if(!Request::has('out_trade_no'))return '请检查参数【out_trade_no】';  
        if(!Request::has('notify_url'))return '请检查参数【notify_url】';  
        if(!Request::has('return_url'))return '请检查参数【return_url】';  
        if(!Request::has('name'))return '请检查参数【name】';  
        if(!Request::has('money'))return '请检查参数【money】';
        if(!Request::has('sign'))return '请检查参数【sign】';
        if(!Request::has('sign_type') && Request::param('sign_type') !== 'MD5')return '请检查参数【sign_type】';  
        $data=Request::param();
       
        if((new Sign())->check($data) && $data['pid']==systemConfig("appid")){
         //if(1==1){
            $model=Db::name('payment');
            $model->startTrans();
            try{
                $data['money'] = number_format($data['money'], 2, '.', '');
                $list=$model->where(['type'=>$data['type'],'status'=>1])->lock(true)->select();
                if(count($list) < 1){
                    if(systemConfig("mail_type") &&systemConfig("empty_notify")){
                           (new Email())->send($data['type'].'暂无可用通道','暂无可用通道通知');
                     }
                     $model->commit();
                    return '暂无可用通道,请稍后再试'; 
                    
                }
                $paytools = $this->create_payment($data['money'],$list);
                if(!$paytools){
                    if(systemConfig("mail_type") &&systemConfig("empty_notify")){
                           (new Email())->send($data['type'].'暂无可用通道','暂无可用通道通知');
                     }
                     $model->commit();
                   return '暂无可用通道,请稍后再试'; 
                }
                $order= new Order;
                $order->type = $data['type'];
                $order->out_trade_no = $data['out_trade_no'];
                $order->notify_url = $data['notify_url'];
                $order->return_url = $data['return_url'];
                $order->name = $data['name'];
                $order->money = $data['money'];
                $order->param = isset($data['param'])?$data['param']:'';
                $order->sign = $data['sign'];
                $order->sign_type = $data['sign_type'];
                $order->trade_no = date("YmdHis").'0000'.rand(100000,999999);
                $order->trade_status = 'TRADE_FAIL';
                $order->create_time = time();
                $order->real_money = number_format($paytools['money'], 2, '.', '');
                $order->payment_id = $paytools['payment']['id'];
                $order->save();
                //$model->where(['id'=>1])->data(['num'=>900])->update();//id为1的更新
                //sleep(10);//等待10秒
                $model->commit();
                
                return redirect('/api/pay/'.$order['trade_no']);

                
            }catch (\Exception $exception){
                $model->rollback();
                throw $exception;
                //return '创建订单失败,请重试';  
            }
            
        }else{
           return '验签失败';  
        }
    }
    public function pay($order){
        $orderInfo = Order::where('trade_no',$order)->find();
        $orderInfo['endtime']=strtotime($orderInfo['create_time'])+300 -time();
        $payment = Payment::find($orderInfo['payment_id']);
        if($payment['type']=='alipay'){
           $orderInfo['payurl']= 'alipays://platformapi/startapp?appId=09999988&actionType=toAccount&goBack=NO&amount='.$orderInfo['real_money'].'&userId='.$payment['image'].'&memo=';
        }else{
           $orderInfo['payurl']= $payment['image'];
        }
        
        View::assign('data',$orderInfo);
        return View::fetch('pay');
    }
    public function pagelisten($order){
        $orderInfo = Order::where('trade_no',$order)->find();
        if($orderInfo['trade_status']=='TRADE_SUCCESS'){
            $param = (new Sign())->create($orderInfo);
            $data = $orderInfo['return_url'].'?'.http_build_query($param);
           return json(['code'=>200,'msg'=>'支付成功','data'=>$data]); 
        }elseif($orderInfo['trade_status']=='TRADE_FAIL'){
           return json(['code'=>201]);  
        }else{
            $param = (new Sign())->create($orderInfo);
            $data = $orderInfo['return_url'].'?'.http_build_query($param);
           return json(['code'=>202,'msg'=>'订单已超时,请重新下单','data'=>$data]); 
        }
        
    }
    /**
     * api提交订单
     * @return json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function submit_api()
    {
         if(!Request::has('pid'))return json(['code'=>201,'msg'=>'请检查参数【pid】']);
        if(!Request::has('type') || (Request::param('type') !=='alipay' && Request::param('type') !=='wxpay' ))return json(['code'=>201,'msg'=>'请检查参数【type】']);
        if(!Request::has('out_trade_no'))return json(['code'=>201,'msg'=>'请检查参数【out_trade_no】']);
        if(!Request::has('notify_url'))return json(['code'=>201,'msg'=>'请检查参数【notify_url】']);
        if(!Request::has('return_url'))return json(['code'=>201,'msg'=>'请检查参数【return_url】']);
        if(!Request::has('name'))return json(['code'=>201,'msg'=>'请检查参数【name】']);
        if(!Request::has('money'))return json(['code'=>201,'msg'=>'请检查参数【money】']);
        if(!Request::has('sign'))return json(['code'=>201,'msg'=>'请检查参数【sign】']);
        if(!Request::has('sign_type') && Request::param('sign_type') !== 'MD5')return '请检查参数【sign_type】';  
        $data=Request::param();
        if((new Sign())->check($data) && $data['pid']==systemConfig("appid")){
            $model=Db::name('payment');
            $model->startTrans();
            try{
                $data['money'] = number_format($data['money'], 2, '.', '');
                $list=$model->where(['type'=>$data['type'],'status'=>1])->lock(true)->select();
                if(count($list) < 1){
                    if(systemConfig("mail_type") &&systemConfig("empty_notify")){
                           (new Email())->send($data['type'].'暂无可用通道','暂无可用通道通知');
                     }
                     $model->commit();
                    return json(['code'=>201,'msg'=>'暂无可用通道']);
                }
                $paytools = $this->create_payment($data['money'],$list);
                if(!$paytools){
                    if(systemConfig("mail_type") &&systemConfig("empty_notify")){
                           (new Email())->send($data['type'].'暂无可用通道','暂无可用通道通知');
                     }
                     $model->commit();
                    return json(['code'=>201,'msg'=>'暂无可用通道']);
                }
                $order= new Order;
                $order->type = $data['type'];
                $order->out_trade_no = $data['out_trade_no'];
                $order->notify_url = $data['notify_url'];
                $order->return_url = $data['return_url'];
                $order->name = $data['name'];
                $order->money = $data['money'];
                $order->param = isset($data['param'])?$data['param']:'';
                $order->sign = $data['sign'];
                $order->sign_type = $data['sign_type'];
                $order->trade_no = date("YmdHis").'0000'.rand(100000,999999);
                $order->trade_status = 'TRADE_FAIL';
                $order->create_time = time();
                $order->real_money =number_format($paytools['money'],2,'.',''); 
                $order->payment_id = $paytools['payment']['id'];
                $order->save();
                
                $model->commit();
                
                return json(['code'=>200,'msg'=>'获取支付链接成功','url'=>Request::domain().'/api/pay/'.$order['trade_no'],'test'=>$paytools]);

                
            }catch (\Exception $exception){
                $model->rollback();
                throw $exception;
            }
            
        }else{
            return json(['code'=>201,'msg'=>'签名不正确']);
        }
    }
    private function create_payment($money,$list,$number=1){
        if($number >count($list))return false;
        $index=$number - 1;
        $payment = $list[$index];
        $moneys = Order::where(['payment_id'=>$payment['id'],'trade_status'=>'TRADE_FAIL'])->column('real_money');
        $data = $this->create_float($money,$moneys,$payment);
        if(!$data){
            $number++;
            return $this->create_payment($money,$list,$number);
        }else{
            return $data;
        }
        
    }
    /* 生成浮动金额 */
    private function create_float($money,$moneys,$payment,$number=0){
        
        if($payment['float_type']==1 && $number >$payment['float_quantity']*2 )return false;
        if($payment['float_type']!=1 && $number >$payment['float_quantity'])return false;
        if($money <= 0)return false;

    	if(in_array($money,$moneys)){
    	    if($payment['float_type'] == 1){
    	        if($number < $payment['float_quantity']){
    	        $money = bcadd($money,$payment['float_unit'],2);
    	        $number++;
    	        return $this->create_float($money,$moneys,$payment,$number);
    	        }
    	        if($number == $payment['float_quantity']){
    	            $money = bcsub(bcsub($money ,($payment['float_quantity']*$payment['float_unit']),2),$payment['float_unit'],2);
    	            $number++;
    	         return $this->create_float($money,$moneys,$payment,$number);
    	        }
    	        if($number > $payment['float_quantity'] && $number <= $payment['float_quantity']*2){
    	        $money = bcsub($money,$payment['float_unit'],2);
    	        $number++;
    	         return $this->create_float($money,$moneys,$payment,$number);
    	        }
    	    }elseif($payment['float_type'] == 2){
    	        $money = bcadd($money,$payment['float_unit'],2);
    	        $number++;
    	         return $this->create_float($money,$moneys,$payment,$number);
    	    }else{
    	        $money = bcsub($money,$payment['float_unit'],2);
    	        $number++;
    	        return $this->create_float($money,$moneys,$payment,$number);
    	    }
    	}else{
    	    $data['money']=$money;
    	    $data['payment']=$payment;
    	    
    	    return $data;
    	}
	}
}
