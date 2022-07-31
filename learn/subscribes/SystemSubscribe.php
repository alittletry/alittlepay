<?php


namespace learn\subscribes;

use learn\workerman\channel\ChannelClient;

/**
 * 系统通知订阅
 * Class SystemSubscribe
 * @package learn\subscribes
 */
class SystemSubscribe
{
    /**
     * 后台通知测试
     * @param $event
     */
    public function onTest($event)
    {
        ChannelClient::instance()->send("Message",[]);
    }
}