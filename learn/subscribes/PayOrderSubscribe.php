<?php


namespace learn\subscribes;

use app\api\model\mini\MiniVideoOrder;
use app\api\model\user\UserOrder;

/**
 * Class PayOrderSubscribe
 * @package learn\subscribes
 */
class PayOrderSubscribe
{
    /**
     * 支付成功异步回调处理
     * @param $event
     */
    public function onPayOrderBefore($event)
    {
        list($payInfo) = $event;
        if ($payInfo && $payInfo['result_code'] == "SUCCESS" && $payInfo['out_trade_no']) UserOrder::orderSuccess($payInfo['out_trade_no']);
    }
}