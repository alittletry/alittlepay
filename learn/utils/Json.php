<?php


namespace learn\utils;

use think\Response;

/**
 * Class Json
 * @package learn\utils
 */
class Json
{
    /**
     * 成功返回码
     * @var int
     */
    private static $SUCCESS_CODE = 200;

    /**
     * 失败返回状态码
     * @var int
     */
    private static $FAIL_CODE = 400;

    /**
     * layui返回状态码
     * @var int
     */
    private static $LAYUI_CODE = 0;

    /**
     * 默认成功返回
     * @var string
     */
    private static $DEFAULT_SUCCESS = 'success';

    /**
     * 默认失败返回
     * @var string
     */
    private static $DEFAULT_FAIL = 'fail';

    /**
     * 返回状态类型
     * @var string
     */
    private $type = 'status';

    /**
     * 实例
     * @param int $status
     * @param string $msg
     * @param array $data
     * @param int $count
     * @return Response
     */
    public function instance(int $status, string $msg, $data = [], int $count=0): Response
    {
        $res['msg'] = $msg;
        $res[$this->type] = $status;
        if ($this->type == 'code') $res['count'] = $count;
        $res['data'] = $data;
        return Response::create($res, 'json', 200);
    }

    /**
     * @param int $status
     * @param string $msg
     * @param array|null $data
     * @return Response
     */
    public function make(int $status, string $msg, ?array $data = null): Response
    {
        $res = compact('status', 'msg');
        if (!is_null($data))
            $res['data'] = $data;
        return Response::create($res, 'json', 200);
    }

    /**
     * 成功返回
     * @param array|string $msg
     * @param array|int $data
     * @return Response
     */
    public function success($msg = '', $data = []): Response
    {
        if (is_array($msg))
        {
            $data = $msg;
            $msg = self::$DEFAULT_SUCCESS;
        }
        elseif (!empty($data) && is_string($data))
        {
            $this->type = $data;
            $data = [];
        }
        if ($msg == '') $msg = self::$DEFAULT_SUCCESS;
        return $this->instance(self::$SUCCESS_CODE,$msg,$data);
    }

    /**
     * 失败返回
     * @param array|string $msg
     * @param array|int $data
     * @return Response
     */
    public function fail($msg = '', $data = []): Response
    {
        if (is_array($msg))
        {
            $data = $msg;
            $msg = self::$DEFAULT_FAIL;
        }
        if ($msg == '') $msg = self::$DEFAULT_SUCCESS;
        return $this->instance(self::$FAIL_CODE,$msg,$data);
    }

    /**
     * layui返回
     * @param array|string $msg
     * @param array $data
     * @return Response
     */
    public function layui($msg = '', ?array $data = []): Response
    {
        $this->type = 'code';
        $count = 0;
        $data=[];
        if (is_array($msg))
        {
            if (!isset($msg['count']) && !isset($msg['data'])) $data = $msg;
            else
            {
                if (isset($msg['count'])) $count = $msg['count'];
                if (isset($msg['data'])) $data = $msg['data'];
            }
            $msg = self::$DEFAULT_SUCCESS;
        }
        if ($msg == '') $msg = self::$DEFAULT_SUCCESS;
        return $this->instance(self::$LAYUI_CODE,$msg,$data,$count);
    }

    /**
     * 返回code字段
     * @return $this
     */
    public function code():self
    {
        $this->type = 'code';
        return $this;
    }
}