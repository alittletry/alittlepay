<?php


namespace learn\subscribes;

use app\admin\model\wechat\WechatMessage;

/**
 * Class MiniProgramMessageSubscribe
 * @package learn\subscribes
 */
class MiniProgramMessageSubscribe
{
    /**
     * 小程序客服前置操作
     * @param $event
     */
    public function onMiniProgramMessageBefore($event)
    {
        list($message) = $event;
        $event = strtolower($message['MsgType']) == 'event' ? strtolower($message['Event']) : strtolower($message['MsgType']) ;
        WechatMessage::saveMessage($message['FromUserName'], $event, json_encode($message,true));
    }
}