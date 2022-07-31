<?php


namespace learn\services;

use app\admin\model\admin\Admin;
use app\admin\model\wechat\WechatReply;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Article;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\Media;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Transfer;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use EasyWeChat\OfficialAccount\Server\Guard;
use think\facade\Cache;
use think\Response;

/**
 * 微信公众号
 * Class WechatService
 * @package learn\services
 */
class WechatService
{
    /**
     * 实例
     * @var null
     */
    private static $instance = null;

    /**
     * 配置项
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function options()
    {
        $wechat = SystemConfigMore(['wechat_appid','wechat_appsecret','wechat_token','wechat_aeskey','wechat_encry']);
        $config = [
            'app_id'=>isset($wechat['wechat_appid']) ? trim($wechat['wechat_appid']) :'',
            'secret'=>isset($wechat['wechat_appsecret']) ? trim($wechat['wechat_appsecret']) :'',
            'token'=>isset($wechat['wechat_token']) ? trim($wechat['wechat_token']) :'',
            'response_type' => 'array',
            'guzzle' => [
                'timeout' => 10.0, // 超时时间（秒）
            ],
        ];
        if(isset($wechat['wechat_encry']) && (int)$wechat['wechat_encry']>1 && isset($wechat['wechat_aeskey']) && !empty($wechat['wechat_aeskey']))
            $config['aes_key'] =  $wechat['wechat_aeskey'];
        return $config;
    }

    /**
     * 应用实例
     * @param bool $cache
     * @return \EasyWeChat\OfficialAccount\Application|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function application($cache = false)
    {
        (self::$instance === null || $cache === true) && (self::$instance = Factory::officialAccount(self::options()));
        return self::$instance;
    }

    /**
     * 服务
     * @return Response
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \ReflectionException
     */
    public static function serve():Response
    {
        $wechat = self::application(true);
        $server = $wechat->server;
        self::hook($server);
        $response = $server->serve();
        return response($response->getContent());
    }

    /**
     * 监听响应
     * @param Guard $server
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     */
    private static function hook($server)
    {
        $server->push(function($message){
            event('MessageBefore',[$message]);
            switch ($message['MsgType']){
                case 'event':
                    switch (strtolower($message['Event'])){
                        case 'subscribe':
                            event('EventSubscribeBefore',[$message]);
                            if (!empty($message['EventKey']) && $param = paramToArray(str_replace("qrscene_","",$message['EventKey'])))
                            {
                                switch ($param['type'])
                                {
                                    case "login":
                                        // 登录操作
                                        if ($param['method'] == 'wechat' && $param['to'] == 'admin')
                                        {
                                            $res = Admin::wechatLogin($message);
                                            if ($res['status'] == 100) $response = "登录成功";
                                            elseif ($res['status'] == 101) $response = "用户不存在";
                                            elseif ($res['status'] == 102) $response = "该用户未绑定管理员账号";
                                            else $response = "未知错误";
                                        }
                                        break;
                                }
                            }else
                            {
                                $response = WechatReply::reply('subscribe');
                            }
                            break;
                        case 'unsubscribe':
                            event('EventUnsubscribeBefore',[$message]);
                            break;
                        case 'scan':
                            event('EventSubscribeBefore',[$message]);
                            if (!empty($message['EventKey']) && $param = paramToArray($message['EventKey']))
                            {
                                switch ($param['type'])
                                {
                                    case "login":
                                        // 登录操作
                                        if ($param['method'] == 'wechat' && $param['to'] == 'admin')
                                        {
                                            $res = Admin::wechatLogin($message);
                                            if ($res['status'] == 100) $response = "登录成功";
                                            elseif ($res['status'] == 101) $response = "用户不存在";
                                            elseif ($res['status'] == 102) $response = "该用户未绑定管理员账号";
                                            else $response = "未知错误";
                                        }
                                        break;
                                }
                            }
                            break;
                        case 'click':
                            $response = WechatReply::reply($message['EventKey']);
                            break;
                    }
                    break;
                case 'text':
                    $response = WechatReply::reply($message['Content']);
                    break;
                default:
                    break;
            }
            return $response;
        });
    }

    /**
     * 多客服消息转发 转发多个或者应该
     * @param string $account
     * @return Transfer
     */
    public static function transfer($account = '')
    {
        return empty($account) ? new Transfer() : new Transfer($account);
    }

    /**
     * 上传永久素材接口
     * @return \EasyWeChat\OfficialAccount\Material\Client
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function materialService()
    {
        return self::application()->material;
    }

    /**
     * 上传临时素材接口
     * @return \EasyWeChat\BasicService\Media\Client
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function mediaService()
    {
        return self::application()->media;
    }

    /**
     * 用户接口
     * @return \EasyWeChat\OfficialAccount\User\UserClient
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function userService()
    {
        return self::application()->user;
    }


    /**
     * 客服消息接口
     * @return \EasyWeChat\OfficialAccount\CustomerService\Client
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function customerServiceService()
    {
        return self::application()->customer_service;
    }

    /**
     * 微信公众号菜单接口
     * @return \EasyWeChat\OfficialAccount\Menu\Client
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function menuService()
    {
        return self::application()->menu;
    }

    /**
     * 微信二维码生成接口
     * @return \EasyWeChat\BasicService\QrCode\Client
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function qrcodeService()
    {
        return self::application()->qrcode;
    }

    /**
     * 短链接生成接口
     * @return \EasyWeChat\BasicService\Url\Client
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function urlService()
    {
        return self::application()->url;
    }

    /**
     * 用户授权
     * @retu'rn \Overtrue\Socialite\Providers\WeChatProvider
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function oauthService()
    {
        return self::application()->oauth;
    }

    /**
     * 模板消息接口
     * @return \EasyWeChat\OfficialAccount\TemplateMessage\Client
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function templateMessageService()
    {
        return self::application()->template_message;
    }

    /**
     * 送模板消息
     * @param string $openid
     * @param string $templateId
     * @param array $data
     * @param string|null $url
     * @param array $miniprogram
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function sendTemplate(string $openid, string $templateId,array $data,string $url = null, array $miniprogram = [])
    {
        return self::templateMessageService()->send([
            'touser' => $openid,
            'template_id' => $templateId,
            'url' => $url,
            'data' => $data,
            'miniprogram' => $miniprogram,
        ]);
    }

    /**
     * 用户标签
     * @return \EasyWeChat\OfficialAccount\User\TagClient
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function userTagService()
    {
        return self::application()->user_tag;
    }

    /**
     * jsSdk
     * @return \EasyWeChat\BasicService\Jssdk\Client
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function jssdkService()
    {
        return self::application()->jssdk;
    }

    /**
     * 生成jssdk
     * @param array $APIs
     * @param string $url
     * @return array|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function jsSdk(array $APIs = [], string $url = '')
    {
        $apiList = $APIs ?: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone', 'startRecord', 'stopRecord', 'onVoiceRecordEnd', 'playVoice', 'pauseVoice', 'stopVoice', 'onVoicePlayEnd', 'uploadVoice', 'downloadVoice', 'chooseImage', 'previewImage', 'uploadImage', 'downloadImage', 'translateVoice', 'getNetworkType', 'openLocation', 'getLocation', 'hideOptionMenu', 'showOptionMenu', 'hideMenuItems', 'showMenuItems', 'hideAllNonBaseMenuItem', 'showAllNonBaseMenuItem', 'closeWindow', 'scanQRCode', 'chooseWXPay', 'openProductSpecificView', 'addCard', 'chooseCard', 'openCard'];
        $jsService = self::jssdkService();
        if($url) $jsService->setUrl($url);
        return $jsService->buildConfig($apiList);
    }

    /**
     * 回复文本消息
     * @param string $content
     * @return Text
     */
    public static function textMessage(string $content)
    {
        return new Text($content);
    }

    /**
     * 回复图片消息
     * @param string $media_id
     * @return Image
     */
    public static function imageMessage(string $media_id)
    {
        return new Image($media_id);
    }

    /**
     * 回复视频消息
     * @param string $mediaId
     * @param array $attributes
     * @return Video
     */
    public static function videoMessage(string $mediaId, array $attributes = [])
    {
        return new Video($mediaId, $attributes);
    }

    /**
     * 回复声音消息
     * @param string $media_id
     * @return Voice
     */
    public static function voiceMessage(string $media_id)
    {
        return new Voice($media_id);
    }

    /**
     * 回复图文消息
     * @param string $title
     * @param string $description
     * @param string $image
     * @param string $url
     * @return News
     */
    public static function newsMessage(string $title, string $description = '...', string $image = '', string $url = '')
    {
        $items = [];
        if(is_array($title)) foreach ($title as $k=>$v) $items[] = new NewsItem(['title'=>$v['title'],'description'=>$v['description'],'image'=>$v['image'],'url'=>$v['url']]);
        else $items[] = new NewsItem(compact("title",'description','image','url'));
        return new News($items);
    }

    /**
     * 回复文章消息
     * @param string $title 标题
     * @param string $author 作者
     * @param string $content 图文消息的具体内容，支持HTML标签，必须少于2万字符，小于1M，且此处会去除JS
     * @param string $thumb_media_id 图文消息的封面图片素材id（必须是永久 media_ID）
     * @param string $digest 图文消息的摘要，仅有单图文消息才有摘要，多图文此处为空
     * @param string $source_url 图文消息的原文地址，即点击“阅读原文”后的URL
     * @param int $show_cover 是否显示封面，0 为 false，即不显示，1 为 true，即显示
     * @return Article
     */
    public static function articleMessage(string $title, string $author = '', string $content='', string $thumb_media_id = '', string $digest = '', string $source_url = '', int $show_cover = 1)
    {
        return new Article(compact('thumb_media_id', 'author', 'title', 'content', 'digest', 'source_url', 'show_cover'));
    }

    /**
     * 回复素材消息
     * @param string $mediaId 素材 ID
     * @param string $type [mpnews、 mpvideo、voice、image]
     * @return Media
     */
    public static function materialMessage(string $mediaId, string $type)
    {
        return new Media($mediaId,$type);
    }

    /**
     * 作为客服消息发送
     * @param $message
     * @param string $openId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function customerServiceTo($message,string $openId)
    {
        $staff = self::customerServiceService();
        return $staff->message($message)->to($openId)->send();
    }

    /**
     * 获得用户信息
     * @param $openid
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getUserInfo($openid)
    {
        $userService = self::userService();
        return is_array($openid) ? $userService->select($openid) : $userService->get($openid);
    }

    /**
     * 获取二维码
     * @param string $param
     * @param int $expire
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function temporary(string $param, int $expire = 300)
    {
        $qrcode = self::qrcodeService();
        $res = $qrcode->temporary($param,$expire);
        return $qrcode->url($res['ticket']);
    }
}
