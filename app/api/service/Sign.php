<?php
namespace app\api\service;


class Sign
{
    public function check($data)
    {
        $sign = $this->getSign($data);
        if($sign == $data['sign']){
            return true;
        }
        return false;
    }
    public function create($order)
    {
        $data['pid']=systemConfig("appid");
        $data['trade_no']=$order['trade_no'];
        $data['out_trade_no']=$order['out_trade_no'];
        $data['type']=$order['type'];
        $data['name']=$order['name'];
        $data['money']=$order['money'];
        $data['trade_status']=$order['trade_status'];
        $data['param']=$order['param'];
        $param = $this->buildRequestParam($data);
        return $param;
    }
    
    private function buildRequestParam($param){
		$mysign = $this->getSign($param);
		$param['sign'] = $mysign;
		$param['sign_type'] = 'MD5';
		return $param;
	}
    private function getSign($param){
		ksort($param);
		reset($param);
		$signstr = '';
	
		foreach($param as $k => $v){
			if($k != "sign" && $k != "sign_type" && $v!=''){
				$signstr .= $k.'='.$v.'&';
			}
		}
		$signstr = substr($signstr,0,-1);
		$signstr .= systemConfig("appkey");
		$sign = md5($signstr);
		return $sign;
	}
}
