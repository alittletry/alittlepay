<?php


namespace learn\services\sms;

use Qcloud\Sms\SmsMultiSender;
use Qcloud\Sms\SmsSingleSender;
use Qcloud\Sms\SmsVoicePromptSender;
use Qcloud\Sms\SmsVoiceVerifyCodeSender;

class QCloudSmsService
{
    /**
     * APPID
     * @var int
     */
    protected $appId = 0;

    /**
     * APPKEY
     * @var string
     */
    protected $appKey = "";

    /**
     * 短信发送助手
     * @var null
     */
    protected $smSender = null;

    /**
     * 需要发送短信的手机号
     * @var array
     */
    protected $phoneNumbers = [];

    /**
     * 实例
     * @var null
     */
    private static $instance = null;

    /**
     * QCloudSmsService constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->appId = isset($config['appId']) ? $config['appId'] : "";
        $this->appKey = isset($config['appKey']) ? $config['appKey'] : "";
    }

    /**
     * 配置
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function options()
    {
        $config = systemConfigMore(["sms_appid","sms_appkey"]);
        return [
            'appId' => $config['sms_appid'],
            'appKey' => $config['sms_appkey']
        ];
    }

    /**
     * @return QCloudSmsService|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function app()
    {
        (self::$instance === null) && (self::$instance = new self(self::options()));
        return self::$instance;
    }

    /**
     * 发送单条短信
     * @param int $templId
     * @param array $params
     * @param string $sign
     * @return bool
     */
    public function sendSingleSms(int $templId, array $params, string $sign)
    {
        try {
            self::$instance->smSender = new SmsSingleSender(self::$instance->appId,self::$instance->appKey);
            $res = json_decode(self::$instance->smSender->sendWithParam("86", self::$instance->phoneNumbers[0], $templId, $params, $sign, "", ""),true);
            return $res['result'] == 0;
        }catch (\Exception $e)
        {
            var_dump($e->getMessage());
            return false;
        }
    }

    /**
     * 发送多条短信
     * @param int $templId
     * @param array $params
     * @param string $sign
     * @return bool
     */
    public function sendMultiSms(int $templId, array $params, string $sign)
    {
        try {
            self::$instance->smSender = new SmsMultiSender(self::$instance->appId,self::$instance->appKey);
            $res = json_decode(self::$instance->smSender->sendWithParam("86", self::$instance->phoneNumbers, $templId, $params, $sign, "", ""),true);
            return $res['result'] == 0;
        }catch (\Exception $e)
        {
            var_dump($e->getMessage());
            return false;
        }
    }

    /**
     * 发送语言短信验证码
     * @param string $verifyCode
     * @return bool
     */
    public function sendVoiceVerifySms(string $verifyCode)
    {
        try {
            self::$instance->smSender = new SmsVoiceVerifyCodeSender(self::$instance->appId,self::$instance->appKey);
            $res = json_decode(self::$instance->smSender->send("86", self::$instance->phoneNumbers[0], $verifyCode),true);
            return $res['result'] == 0;
        }catch (\Exception $e)
        {
            var_dump($e->getMessage());
            return false;
        }
    }

    /**
     * 发送语言通知
     * @param string $msg
     * @return bool
     */
    public function sendVoicePromptSms(string $msg)
    {
        try {
            self::$instance->smSender = new SmsVoicePromptSender(self::$instance->appId,self::$instance->appKey);
            $res = json_decode(self::$instance->smSender->send("86", self::$instance->phoneNumbers[0], 2, $msg),true);
            return $res['result'] == 0;
        }catch (\Exception $e)
        {
            var_dump($e->getMessage());
            return false;
        }
    }

    /**
     * 设置发送的手机号
     * @param array $phone
     * @return null
     */
    public function setPhoneNumbers(array $phone)
    {
        self::$instance->phoneNumbers = is_array($phone) ? $phone : [$phone];
        return self::$instance;
    }
}