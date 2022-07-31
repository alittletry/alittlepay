<?php


namespace learn\workerman\admin;

use Channel\Client;
use learn\workerman\channel\ChannelClient;
use learn\workerman\Response;
use think\worker\Server;
use Workerman\Connection\TcpConnection;
use Workerman\Lib\Timer;
use Workerman\Worker;

/**
 * 后台ws服务
 * Class worker
 * @package learn\workerman\admin
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
    protected $port = 1996;

    /**
     * 基础配置
     * @var array
     */
    protected $option = [
        'count'		=> 1,
        'name'		=> 'admin'
    ];

    /**
     * 定时程序
     * @var null
     */
    protected $time;

    /**
     * @var Worker
     */
    protected $worker;

    /**
     * @var TcpConnection[]
     */
    protected $connections = [];

    /**
     * @var TcpConnection[]
     */
    protected $user = [];

    /**
     * @var WorkerHandle
     */
    protected $handle;

    /**
     * @var Response
     */
    protected $response;

    public function setUser(TcpConnection $connection)
    {
        $this->user[$connection->adminInfo['id']] = $connection;
    }

    /**
     * worker constructor.
     * @param Worker|null $worker
     */
    protected function init(Worker $worker = null)
    {
        parent::init();
        $this->worker = $worker;
        $this->handle = new WorkerHandle($this);
        $this->response = new Response();
    }

    /**
     * 连接
     * @param TcpConnection $connection
     */
    public function onConnect(TcpConnection $connection)
    {
        $this->connections[$connection->id] = $connection;
        $connection->lastMessageTime = time();
    }

    /**
     * 当获取到信息
     * @param TcpConnection $connection
     * @param $res
     * @return bool|void|null
     */
    public function onMessage(TcpConnection $connection, $res)
    {
        $connection->lastMessageTime = time();
        $res = json_decode($res, true);
        if (!$res || !isset($res['type']) || !$res['type']) return;
        if ($res['type'] == 'ping') return $this->response->connection($connection)->send('pong');
        if (!method_exists($this->handle, $res['type'])) return;
        $this->handle->{$res['type']}($connection, $res + ['data' => []], $this->response->connection($connection));
    }

    /**
     * 开启时
     * @param Worker $worker
     * @throws \Exception
     */
    public function onWorkerStart(Worker $worker)
    {
        // 开启订阅
        ChannelClient::connet();
        Client::on('learn', function ($eventData) use ($worker) {
            if (!isset($eventData['type']) || !$eventData['type']) return;
            $ids = isset($eventData['ids']) && count($eventData['ids']) ? $eventData['ids'] : array_keys($this->user);
            foreach ($ids as $id) {
                if (isset($this->user[$id]))
                    $this->response->connection($this->user[$id])->success($eventData['type'], $eventData['data'] ?? null);
            }
        });

        // 超时关闭
        Timer::add(35, array($this->handle, 'timeoutClose'), array($worker,$this->response), true);
    }

    /**
     * 连接关闭
     * @param TcpConnection $connection
     */
    public function onClose(TcpConnection $connection)
    {
        unset($this->connections[$connection->id]);
    }
}