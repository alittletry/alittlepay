<?php


namespace app\admin\controller;

use app\admin\model\mini\MiniVideo;
use app\admin\model\wechat\WechatNews;
use app\admin\model\wechat\WechatUser;
use app\api\model\user\UserOrder;
use app\models\user\UserBill;
use app\Request;
use learn\services\crawler\KanRankService;
use learn\services\crawler\KanService;
use learn\services\crawler\QQService;
use learn\services\ExcelService;
use learn\services\mail\MailService;
use learn\services\pay\PayService;
use learn\services\sms\QCloudSmsService;
use learn\services\storage\QcloudCoService;
use learn\services\WechatService;
use learn\utils\Curl;
use think\facade\Cache;

class Test extends AuthController
{
    // 无需登录的
    protected $noNeedLogin = [];
    // 无需权限的
    protected $noNeedRight = [];

    public function index()
    {
        echo "测试";
    }
}
