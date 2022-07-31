<?php
// 事件定义文件
return [
    'bind'      => [
    ],

    'listen'    => [
        'Task_1'=>[],//1秒钟执行的方法
        'Task_5'=>[],//5秒钟执行的方法
        'Task_10'=>[],//10秒钟执行的方法
        'Task_30'=>[],//30秒钟执行的方法
        'Task_60'=>[],//60秒钟执行的方法
        'AppInit'  => [],
        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
        'Test' => [],  // 后台通知测试
        'AdminLog' => [], // 操作日志记录
        'MessageBefore' => [], //微信信息前置操作,
        'MiniProgramMessageBefore' => [], //小程序客服前置操作
        'EventUnsubscribeBefore' => [], //微信取消关注前置操作
        'EventSubscribeBefore' => [], //微信关注前者操作
        'UploadMediaAfter' => [], // 微信上传素材文件
        'PayOrderBefore' => [], // 订单支付成功回调,
        'VideoUpdateOver' => [], //电视剧更新完成记录,
        'VideoRankUpdateOver' => [], //排行榜更新,
        'UploadMaterialAfter' => [], // 图文素材
    ],

    'subscribe' => [
        \learn\subscribes\AdminSubscribe::class, // 操作记录
        \learn\subscribes\SystemSubscribe::class, // 系统通知
        \learn\subscribes\TimerSubscribe::class, // 定时器
        \learn\subscribes\WechatMessageSubscribe::class, //微信操作
        \learn\subscribes\WechatMediaSubscribe::class, // 微信素材
        \learn\subscribes\PayOrderSubscribe::class, // 订单成功回调
        \learn\subscribes\MiniProgramMessageSubscribe::class, //小程序客服
        \learn\subscribes\VideoSubscribe::class, //视频操作
    ],
];
