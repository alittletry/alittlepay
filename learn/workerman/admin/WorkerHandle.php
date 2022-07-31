<?php


namespace learn\workerman\admin;

use app\admin\model\admin\Admin;
use learn\services\WechatService;
use learn\workerman\Response;
use learn\workerman\admin\WorkerService;
use think\facade\Cache;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;
use think\facade\Session;
use learn\utils\Session as MySession;

/**
 * Class WorkerHandle
 * @package learn\workerman\admin
 */
class WorkerHandle
{
    protected $service;

    public function __construct(WorkerService &$service)
    {
        $this->service = &$service;
    }

    /**
     * 后台登录
     * @param TcpConnection $connection
     * @param array $res
     * @param Response $response
     * @return bool|null
     */
    public function login(TcpConnection &$connection, array $res, Response $response)
    {
        if (!isset($res['data']) || !$sessionId = $res['data']) {
            return $response->close([
                'msg' => '授权失败!'
            ]);
        }

        MySession::setId($sessionId);

        if (!Session::has('adminId') || !Session::has('adminInfo')) {
            return $response->close([
                'msg' => '授权失败!'
            ]);
        }

        $connection->adminInfo = Session::get('adminInfo');
        $connection->sessionId = $sessionId;

        $this->service->setUser($connection);

        return $response->success();
    }

    /**
     * 超时关闭
     * @param Worker $worker
     * @param Response $response
     */
    public function timeoutClose(Worker $worker,Response $response)
    {
        $time_now = time();
        foreach ($worker->connections as $connection) {
            if ($time_now - $connection->lastMessageTime > 28) {
                $response->connection($connection)->close('timeout');
            }
        }
    }

    /**
     * 后台微信扫码登录
     * @param TcpConnection $connection
     * @param array $res
     * @param Response $response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function qrcode(TcpConnection &$connection, array $res, Response $response)
    {
        MySession::setId($res['token']);
        // 保存缓存
        if (Cache::store('redis')->has($res['token']))
        {
            $response->connection($connection)->send('qrcode',['src'=>Cache::store('redis')->get($res['token'])]);
        }
        else
        {
            $qrcode = WechatService::temporary("type=login&method=wechat&to=admin&token=$res[token]",180);
            Cache::store('redis')->set($res['token'],$qrcode,180);
            $response->connection($connection)->send('qrcode',['src'=>$qrcode]);
        }
    }

    /**
     * 验证二维码是否有效
     * @param TcpConnection $connection
     * @param array $res
     * @param Response $response
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function valid(TcpConnection &$connection, array $res, Response $response)
    {
        try {
            if (Cache::store('redis')->has("info_".$res['token']))
            {
                if (Admin::setLoginInfo(Cache::store('redis')->get("info_".$res['token'])))
                {
                    $response->connection($connection)->close('valid',['status'=>200]);
                    Cache::store('redis')->delete($res['token']);
                    Cache::store('redis')->delete("info_".$res['token']);
                }
            }
            elseif(Cache::store('redis')->has($res['token'])) $response->connection($connection)->send('valid',['status'=>300]);
            else $response->connection($connection)->close('valid',['status'=>400]);
        }catch (\Exception $e)
        {
            file_put_contents("wsLogin.log",$e->getMessage());
            $response->connection($connection)->close('valid',['status'=>200]);
        }
    }
}