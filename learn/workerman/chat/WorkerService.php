<?php


namespace learn\workerman\chat;


use think\worker\Server;

/**
 * 客服聊天
 * Class Worker
 * @package learn\workerman\chat
 */
class WorkerService extends Server
{
    /**
     * 协议
     * @var string
     */
    protected $protocol = "websocket";

    /**
     * 监听地址
     * @var string
     */
    protected $host = '0.0.0.0';

    /**
     * 端口
     * @var string
     */
    protected $port = 1997;

    /**
     * 基础配置
     * @var array
     */
    protected $option = [
        'count'		=> 1,
        'name'		=> 'chat'
    ];

}