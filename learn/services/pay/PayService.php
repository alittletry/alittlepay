<?php


namespace learn\services\pay;

use Yansongda\Pay\Pay;

/**
 * 支付
 * Class PayService
 * @package learn\services\pay
 */
class PayService
{
    /**
     * 支付类型 支付宝 or 微信
     * @var null
     */
    public $type = null;

    /**
     * 支付方式
     * @var null
     */
    public $method = null;

    /**
     * 实例
     * @var null
     */
    public static $instance = null;

    /**
     * 配置
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function options()
    {
        switch ($this->type)
        {
            case 'wechat':
                $params = systemConfigMore(['pay_wechat_appid','pay_wechat_app_id','pay_wechat_miniapp_id','pay_wechat_mch_id','pay_wechat_key','pay_wechat_apiclient_cert','pay_wechat_apiclient_key']);
                $config = [
                    'appid' => $params['pay_wechat_appid'], // APP APPID
                    'app_id' => $params['pay_wechat_app_id'], // 公众号 APPID
                    'miniapp_id' => $params['pay_wechat_miniapp_id'], // 小程序 APPID
                    'mch_id' => $params['pay_wechat_mch_id'],
                    'key' => $params['pay_wechat_key'],
                    'notify_url' => self::notify_url($this->type,$this->method),
                    'cert_client' => realpath(".".$params['pay_wechat_apiclient_cert']), // optional，退款等情况时用到
                    'cert_key' => realpath(".".$params['pay_wechat_apiclient_key']),// optional，退款等情况时用到
                    'log' => [ // optional
                        'file' => './logs/wechat.log',
                        'level' => 'debug', // 建议生产环境等级调整为 info，开发环境为 debug
                        'type' => 'single', // optional, 可选 daily.
                        'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
                    ],
                    'http' => [ // optional
                        'timeout' => 5.0,
                        'connect_timeout' => 5.0,
                        // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
                    ],
                    //'mode' => 'dev', // optional, dev/hk;当为 `hk` 时，为香港 gateway。 设置就是沙箱环境
                ];
                break;
            case 'alipay':
                $params = systemConfigMore(['pay_alipay_app_id','pay_alipay_ali_public_key','pay_alipay_private_key','pay_alipay_app_cert_public_key','pay_alipay_alipay_root_cert']);
                $config = [
                    'app_id' => $params['pay_alipay_app_id'],
                    'notify_url' => 'http://yansongda.cn/notify.php',
                    'return_url' => 'http://yansongda.cn/return.php',
                    'ali_public_key' => $params['pay_alipay_ali_public_key'],
                    // 加密方式： **RSA2**
                    'private_key' => $params['pay_alipay_private_key'],
                    // 使用公钥证书模式，请配置下面两个参数，同时修改ali_public_key为以.crt结尾的支付宝公钥证书路径，如（./cert/alipayCertPublicKey_RSA2.crt）
                    // 'app_cert_public_key' => './cert/appCertPublicKey.crt', //应用公钥证书路径
                    // 'alipay_root_cert' => './cert/alipayRootCert.crt', //支付宝根证书路径
                    'log' => [ // optional
                        'file' => './logs/alipay.log',
                        'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
                        'type' => 'single', // optional, 可选 daily.
                        'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
                    ],
                    'http' => [ // optional
                        'timeout' => 5.0,
                        'connect_timeout' => 5.0,
                        // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
                    ],
//                    'mode' => 'dev', // optional,设置此参数，将进入沙箱模式
                ];
                break;
            default:
                $config = [];
        }
        return $config;
    }

    /**
     * 回调地址
     * @param string $type
     * @param string $method
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function notify_url(string $type, string $method)
    {
        if ($type == "wechat")
        {
            switch ($method)
            {
                case 'mp':
                    return systemConfig("domain").Url("/api/wechat/notify");
                case 'miniapp':
                    return systemConfig("domain").Url("/api/mini_program/notify");
                case 'scan':
                    return systemConfig("domain").Url("/api/wechat/notify");
            }
        }
        elseif ($type == "alipay")
        {
            return "";
        }
    }

    /**
     * PayService constructor.
     * @param string $type
     * @param string $method
     */
    public function __construct(string $type, string $method)
    {
        $this->type = $type;
        $this->method = $method;
    }

    /**
     * app
     * @param string $type
     * @param string $method
     * @return PayService|null
     */
    public static function app(string $type, string $method)
    {
        (self::$instance === null) && (self::$instance = new self($type,$method));
        return self::$instance;
    }

    /**
     * 支付服务
     * @return mixed
     */
    public static function serve()
    {
        return Pay::{self::$instance->type}(self::$instance->options());
    }

    /**
     * 支付
     * @param array $order
     * @return bool
     */
    public function pay(array $order)
    {
        try {
           return self::serve()->{self::$instance->method}($order);
        }catch (\Exception $e)
        {
            var_dump($e->getMessage());
            return false;
        }
    }

    /**
     * 同步回调
     * @return bool
     */
    public function return()
    {
        try {
            return self::serve()->verify();
        }catch (\Exception $e)
        {
            var_dump($e->getMessage());
            return false;
        }
        return self::serve()->success()->send();
    }

    /**
     * 异步支付回调
     * @return bool
     */
    public function notify()
    {
        try {
            event("PayOrderBefore",[self::serve()->verify()]);
        }catch (\Exception $e)
        {
            var_dump($e->getMessage());
        }
        return self::serve()->success()->send();
    }
}