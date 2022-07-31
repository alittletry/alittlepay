<?php
namespace app;

// 应用请求对象类
use Spatie\Macroable\Macroable;

/**
 * Class Request
 * @package app
 */
class Request extends \think\Request
{
    // 宏指令
    use Macroable;
}
