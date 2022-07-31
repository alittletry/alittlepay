<?php


namespace learn\services;


use app\admin\model\wechat\WechatReply;
use EasyWeChat\Factory;

/**
 * 小程序
 * Class MiniProgramService
 * @package learn\services
 */
class MiniProgramService
{
    /**
     * 实例
     * @var null
     */
    private static $instance = null;

    /**
     * 配置参数
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function options()
    {
        $wechat = SystemConfigMore(['miniprogram_appid','miniprogram_appsecret','miniprogram_token','miniprogram_aeskey','miniprogram_encry']);
        return [
            'app_id'=>isset($wechat['miniprogram_appid']) ? trim($wechat['miniprogram_appid']):'',
            'secret'=>isset($wechat['miniprogram_appsecret']) ? trim($wechat['miniprogram_appsecret']):'',
            'token' =>isset($wechat['miniprogram_token']) ? trim($wechat['miniprogram_token']):'',
            'aes_key' =>isset($wechat['miniprogram_aeskey']) ? trim($wechat['miniprogram_aeskey']):'',
            'response_type' => 'array',
        ];
    }

    /**
     * 应用
     * @param bool $cache
     * @return \EasyWeChat\MiniProgram\Application|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function application($cache = false)
    {
        (self::$instance === null || $cache === true) && (self::$instance = Factory::miniProgram(self::options()));
        return self::$instance;
    }

    /**
     * 小程序客服
     * @return \think\Response
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function serve()
    {
        $wechat = self::application(true);
        $server = $wechat->server;
        self::hook($server);
        $response = $server->serve();
        return response($response->getContent());
    }

    /**
     * 小程序客服信息
     * @param $server
     */
    private static function hook($server)
    {
        $server->push(function($message){
            event('MiniProgramMessageBefore',[$message]);
            switch ($message['MsgType']){
                case 'text':
                    self::sendService($message['FromUserName'], WechatReply::miniReply($message['Content']));
                    break;
                default:
                    break;
            }
        });
    }

    /**
     * 接口
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function miniProgram()
    {
        return self::application();
    }

    /**
     * auth
     * @return \EasyWeChat\MiniProgram\Auth\Client
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function auth()
    {
        return self::miniProgram()->auth;
    }

    /**
     * session
     * @param string $code
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function session(string $code)
    {
        return self::auth()->session($code);
    }

    /**
     * 客服消息接口
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function staffService()
    {
        return self::miniProgram()->customer_service;
    }

    /**
     * 回复客服文本消息
     * @param string $openid
     * @param array $message
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function sendService(string $openid, array $message)
    {
        $message['touser'] = $openid;
        self::staffService()->send($message);
        return "success";
    }

    /**
     * 文字信息
     * @param string $content
     * @return array
     */
    public static function textMessage(string $content)
    {
        return [
            "msgtype"=>"text",
            "text" => [
                "content"=> $content,
            ],
        ];
    }

    /**
     * 图片信息
     * @param string $media_id
     * @return array
     */
    public static function imageMessage(string $media_id)
    {
        return [
            "msgtype"=>"image",
            "image" => [
                "media_id"=> $media_id,
            ],
        ];
    }

    /**
     * @param string $title
     * @param string $description
     * @param string $url
     * @param string $thumb_url
     * @return array
     */
    public static function linkMessage(string $title, string $description, string $url, string $thumb_url)
    {
        return [
            "msgtype"=>"link",
            "link" => [
                "title"=> $title,
                "description"=> $description,
                "url"=> $url,
                "thumb_url"=> $thumb_url,
            ],
        ];
    }

    /**
     * 回复小程序
     * @param string $title
     * @param string $pagepath
     * @param string $thumb_media_id
     * @return array
     */
    public static function miniprogrampageMessage(string $title, string $pagepath, string $thumb_media_id)
    {
        return [
            "msgtype"=>"miniprogrampage",
            "miniprogrampage" => [
                "title"=> $title,
                "pagepath"=> $pagepath,
                "thumb_media_id"=> $thumb_media_id,
            ],
        ];
    }

    /**
     * 加密数据解密
     * @param $sessionKey
     * @param $iv
     * @param $encryptData
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function encryptor($sessionKey, $iv, $encryptData){
        return self::miniProgram()->encryptor->decryptData($sessionKey, $iv, $encryptData);
    }

    /**
     * 微信小程序二维码生成接口
     * @return \EasyWeChat\MiniProgram\AppCode\Client
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function qrCodeService()
    {
        return self::miniProgram()->app_code;
    }

    /**
     * 模板消息接口
     * @return \EasyWeChat\MiniProgram\TemplateMessage\Client
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function templateMessageService()
    {
        return self::miniProgram()->template_message;
    }

    /**
     * 发送小程序模版消息
     * @param $openid
     * @param $templateId
     * @param array $data
     * @param $form_id
     * @param null $url
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function sendTemplate($openid,$templateId,array $data,$form_id,$url = null)
    {
        return self::templateMessageService()->send([
            'touser' => $openid,
            'template_id' => $templateId,
            'page' => $url,
            'form_id' => $form_id,
            'data' => $data
        ]);
    }
}