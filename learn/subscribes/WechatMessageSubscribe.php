<?php


namespace learn\subscribes;

use app\admin\model\wechat\WechatMessage;
use app\admin\model\wechat\WechatUser;

/**
 * Class WechatMessageSubscribe
 * @package learn\subscribes
 */
class WechatMessageSubscribe
{
    /**
     * 微信公众号前置操作
     * @param $event
     */
    public function onMessageBefore($event)
    {
        list($message) = $event;
        $event = strtolower($message['MsgType']) == 'event' ? strtolower($message['Event']) : strtolower($message['MsgType']) ;
        WechatMessage::saveMessage($message['FromUserName'], $event, json_encode($message,true));
    }

    /**
     * 用户信息前置操作
     * @param $event
     */
    public function onMiniProgramMessageBefore($event)
    {
        list($message) = $event;
        $event = strtolower($message['MsgType']) == 'event' ? strtolower($message['Event']) : strtolower($message['MsgType']);
        WechatMessage::saveMessage($message['FromUserName'], $event, json_encode($message,true));
    }

    /**
     * 取消订阅事件
     * @param $event
     */
    public function onEventUnsubscribeBefore($event)
    {
        list($message) = $event;
        WechatUser::unSubscribe($message['FromUserName']);
    }

    /**
     * 订阅事件
     * @param $event
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function onEventSubscribeBefore($event)
    {
        list($message) = $event;
        WechatUser::subscribe($message['FromUserName']);
    }
}