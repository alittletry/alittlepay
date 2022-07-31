<?php


namespace learn\workerman\channel;


use think\worker\Server;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class ChannelService extends Server
{
    /**
     * 协议
     * @var string
     */
    protected $protocol = "frame";

    /**
     * 监听地址
     * @var string
     */
    protected $host = '0.0.0.0';

    /**
     * 端口
     * @var string
     */
    protected $port = 1998;

    /**
     * Worker instance.
     * @var Worker
     */
    protected $_worker = null;

    /**
     * 基础配置
     * @var array
     */
    protected $option = [
        'count'		=> 1,
        'name'		=> 'ChannelServer'
    ];

    /**
     * 事件
     * @var array
     */
    protected $event    = ['onMessage', 'onClose'];

    /**
     * 实例化
     */
    public function init()
    {
        $this->worker->channels = array();
        $this->_worker = $this->worker;
        parent::init();
    }

    /**
     * onClose
     * @param $connection
     * @return void
     */
    public function onClose($connection)
    {
        if(empty($connection->channels))
        {
            return;
        }
        foreach($connection->channels as $channel)
        {
            unset($this->_worker->channels[$channel][$connection->id]);
            if(empty($this->_worker->channels[$channel]))
            {
                unset($this->_worker->channels[$channel]);
            }
        }
    }

    /**
     * onMessage.
     * @param TcpConnection $connection
     * @param string $data
     */
    public function onMessage($connection, $data)
    {
        if(!$data)
        {
            return;
        }
        $worker = $this->_worker;
        $data = unserialize($data);
        $type = $data['type'];
        $channels = $data['channels'];
        switch($type)
        {
            case 'subscribe':
                foreach($channels as $channel)
                {
                    $connection->channels[$channel] = $channel;
                    $worker->channels[$channel][$connection->id] = $connection;
                }
                break;
            case 'unsubscribe':
                foreach($channels as $channel)
                {
                    if(isset($connection->channels[$channel]))
                    {
                        unset($connection->channels[$channel]);
                    }
                    if(isset($worker->channels[$channel][$connection->id]))
                    {
                        unset($worker->channels[$channel][$connection->id]);
                        if(empty($worker->channels[$channel]))
                        {
                            unset($worker->channels[$channel]);
                        }
                    }
                }
                break;
            case 'publish':
                foreach($channels as $channel)
                {
                    if(empty($worker->channels[$channel]))
                    {
                        continue;
                    }
                    $buffer = serialize(array('channel'=>$channel, 'data' => $data['data']))."\n";
                    foreach($worker->channels[$channel] as $connection)
                    {
                        $connection->send($buffer);
                    }
                }
                break;
        }
    }
}