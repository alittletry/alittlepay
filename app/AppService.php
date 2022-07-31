<?php
declare (strict_types = 1);

namespace app;

use learn\utils\Json;
use think\Service;

/**
 * 应用服务类
 */
class AppService extends Service
{
    public $bind = [
        'json' => Json::class
    ];

    public function register()
    {
        // 服务注册
    }

    public function boot()
    {
        // 服务启动
    }
}
