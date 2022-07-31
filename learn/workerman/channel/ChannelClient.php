<?php


namespace learn\workerman\channel;


use Channel\Client;

class ChannelClient
{
    /**
     * @var Client
     */
    protected $channel;

    /**
     * @var ChannelClient
     */
    protected static $instance;

    /**
     * 监听地址
     * @var string
     */
    const LISTENHOST = '0.0.0.0';

    /**
     * 监听端口
     * @var string
     */
    const LISTENPORT = 1998;

    public function __construct()
    {
        self::connet();
    }

    public static function instance()
    {
        if (is_null(self::$instance))
            self::$instance = new self();

        return self::$instance;
    }

    public static function connet()
    {
        Client::connect(self::LISTENHOST, self::LISTENPORT);
    }

    /**
     * 发送消息
     * @param string $type 类型
     * @param array|null $data 数据
     * @param array|null $ids 用户 id,不传为全部用户
     */
    public function send(string $type, ?array $data = null, ?array $ids = null)
    {
        $res = compact('type');
        if (!is_null($data))
            $res['data'] = $data;

        if (!is_null($ids) && count($ids))
            $res['ids'] = $ids;

        $this->trigger('learn', $res);
    }

    public function trigger(string $type, ?array $data = null)
    {
        Client::publish($type, $data);
    }
}