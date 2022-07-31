<?php


namespace learn\subscribes;

use app\admin\model\mini\MiniVideo;

/**
 * Class TimerSubscribe
 * @package learn\subscribes
 */
class TimerSubscribe
{
    /**
     * 每隔1秒执行的任务
     */
    public function onTask_1()
    {
    }

    /**
     * 每隔5秒执行的任务
     */
    public function onTask_5()
    {
    }

    /**
     * 每隔10秒执行的任务
     */
    public function onTask_10()
    {
    }

    /**
     * 每隔30秒执行的任务
     */
    public function onTask_30()
    {
    }

    /**
     * 每隔60秒执行的任务
     */
    public function onTask_60()
    {
    }

    /**
     * 每隔4小时执行的任务
     */
    public function onTask_14400()
    {
        /**
         * 更新电视剧
         */
        MiniVideo::UpdateTimer();
    }

    /**
     * 每隔一天执行的任务
     */
    public function onTask_86400()
    {
        /**
         * 更新视频排行榜
         */
//        MiniVideo::updateVideoRank("movie");
        MiniVideo::updateVideoRank("tv");
    }
}