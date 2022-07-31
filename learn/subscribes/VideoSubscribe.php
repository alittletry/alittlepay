<?php


namespace learn\subscribes;

use app\admin\model\admin\AdminNotify;

/**
 * 视频事件订阅
 * Class VideoSubscribe
 * @package learn\subscribes
 */
class VideoSubscribe
{
    /**
     * 视频更新完成
     * @param $event
     */
    public function onVideoUpdateOver($event)
    {
        list($data) = $event;
        AdminNotify::addLog([
            'aid' => 1,
            'title' => "电视剧定时更新".date("Y-m-d H:i:s"),
            'content' => "尝试更新的视频有：".implode("、",array_column($data,"title"))."。",
            'type' => 'timer',
            'add_time' => time()
        ]);
    }

    /**
     * 排行榜更新
     * @param $event
     */
    public function onVideoRankUpdateOver($event)
    {
        list($data) = $event;
        AdminNotify::addLog([
            'aid' => 1,
            'title' => "360排行榜定时更新".date("Y-m-d H:i:s"),
            'content' => "尝试更新的视频有：".implode("、",$data)."。",
            'type' => 'timer',
            'add_time' => time()
        ]);
    }
}