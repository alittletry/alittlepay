<?php


namespace learn\services\crawler;

use learn\utils\Curl;
/**
 * 爬取视频排行榜
 * Class KanRankService
 * http://www.360kan.com/rank/dianying 电影
 * http://www.360kan.com/rank/dianshi 电视剧
 * @package learn\services\crawler
 */
class KanRankService
{
    /**
     * 实例
     * @var null
     */
    private static $instance = null;

    /**
     * 电影排行榜链接
     */
    const MV_URL = "http://www.360kan.com/rank/dianying";

    /**
     * 电视剧排行榜链接
     */
    const TV_URL = "http://www.360kan.com/rank/dianshi";

    /**
     * 视频地址
     */
    const URL_MATCH = "/<p class=\"m-title\"><a href=\"([^<>]+)\" title=\"([^<>]+)\">([^<>]+)<\/a><\/p>/";

    /**
     * QQService constructor.
     * @param string|null $type
     */
    public function __construct(string $type = null)
    {
        if ($type !== null) $this->type = $type;
        if ($type == "tv")
        {
            $this->url = self::TV_URL;
        }elseif ($type == "movie")
        {
            $this->url = self::MV_URL;
        }
    }

    /**
     * @param string|null $type
     * @return QQService|null
     */
    public static function app(string $type = null)
    {
        self::$instance = new self($type);
        return self::$instance;
    }

    /**
     * 获取链接内容
     * 去掉空格换行等
     */
    public function openUrl()
    {
        $this->html = Curl::app($this->url)->header(["User-Agent: Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50"])->run();
        $this->html = preg_replace("/[\t\n\r]+/","",$this->html);
    }

    /**
     * 获取视频信息
     */
    public function run()
    {
        try {
            $this->openUrl();
            $list = self::getUrl();
            return compact("list");
        }catch (\Exception $e)
        {

        }
    }

    /**
     * 获取360视频地址
     */
    public function getUrl()
    {
        preg_match_all(self::URL_MATCH,$this->html,$url);
        return [$url[1],$url[2]];
    }
}