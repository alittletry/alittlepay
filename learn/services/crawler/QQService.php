<?php


namespace learn\services\crawler;

use learn\utils\Curl;

/**
 * 腾讯视频爬虫
 * Class QQVideoService
 * @package learn\services\crawler
 */
class QQService
{
    /**
     * 实例
     * @var null
     */
    private static $instance = null;

    /**
     * 详情页url
     * @var string
     */
    private $detail_url = "https://v.qq.com/detail/m/";

    /**
     * 视频详情地址
     * @var
     */
    public $url = null;

    /**
     * 视频类型
     * @var null
     */
    public $type = "movie";

    /**
     * html
     * @var null
     */
    public $html = null;

    /**
     * 标题
     */
    const TITLE_MATCH = "/<a r-attr=\"([^<>]+)\" target=\"_blank\" _stat=\"info:title\">([^<>]+)<\/a>/";

    /**
     * 简介
     */
    const DESC_MATCH = "/<span class=\"txt _desc_txt_lineHight\" itemprop=\"description\">([^<>]+)<\/span>/";

    /**
     * 上映时间
     */
    const TIME_MATCH = "/<div class=\"type_item\"><span class=\"type_tit\">上映时间:<\/span><span class=\"type_txt\">([^<>]+)<\/span><\/div>/";

    /**
     * 出品时间-电视剧
     */
    const mTIME_MATCH = "/<div class=\"type_item\"><span class=\"type_tit\">出品时间:<\/span><span class=\"type_txt\">([^<>]+)<\/span><\/div>/";
    /**
     * 封面
     */
    const COVER_MATCH = "/<img class=\"figure_pic\" src=\"([^<>]+)\" alt=\"([^<>]+)\" itemprop=\"image\" _stat=\"info:poster\"\/>/";

    /**
     * 标签
     */
    const TAG_MATCH = "/<a class=\"tag\" href=\"([^<>]+)\" target=\"_blank\" _stat=\"info:tag\">([^<>]+)<\/a>/";

    /**
     * 演员
     */
    const ACTOR_MATCH = "/<a href=\"([^<>]+)\" target=\"_blank\" class=\"actor_img\" _stat=\"info:card_avatar\"><img class=\"actor_pic\" src=\"([^<>]+)\"><\/a><div class=\"actor_detail\"><h2 class=\"actor_name\"><a href=\"([^<>]+)\" target=\"_blank\" _stat=\"info:card_name\">([^<>]+)<\/a><\/h2>/";

    /**
     * 总集数
     */
    const NUM_MATCH = "/<div class=\"type_item\"><span class=\"type_tit\">总集数:<\/span><span class=\"type_txt\">([^<>]+)<\/span><\/div>/";

    /**
     * 每集信息
     */
    const ITEM_MATCH = "/<a href=\"([^<>]+)\" target=\"_blank\"itemprop=\"url\"><span itemprop=\"episodeNumber\">([^<>]+)<\/span>(.*?)/";

    /**
     * QQService constructor.
     * @param string $vid
     * @param string|null $type
     */
    public function __construct(string $vid, string $type = null)
    {
        $this->url = $this->detail_url.$vid.".html";
        if ($type !== null) $this->type = $type;
    }

    /**
     * @param string $vid
     * @param string|null $type
     * @return QQService|null
     */
    public static function app(string $vid,string $type = null)
    {
        (self::$instance == null) && (self::$instance = new self($vid,$type));
        return self::$instance;
    }

    /**
     * 获取链接内容
     * 去掉空格换行等
     */
    public function openUrl()
    {
        $this->html = Curl::app($this->url)->run();
        $this->html = preg_replace("/[\t\n\r]+/","",$this->html);
    }

    /**
     * 获取视频信息
     */
    public function message()
    {
        try {
            $this->openUrl();
            $title = self::title();
            $desc = self::desc();
            $time = self::time();
            $cover = self::cover();
            $tag = self::tag();
            $actor = self::actor();
            switch ($this->type)
            {
                case "movie":
                    return compact("title","desc","time","cover","tag","actor");
                case "tv":
                    $num = self::num();
                    $item = self::item();
                    $now_num = count($item);
                    return compact("title","desc","time","cover","tag","actor","num","item","now_num");
            }
        }catch (\Exception $e)
        {

        }
    }

    /**
     * 视频标题
     * @return mixed
     */
    public function title()
    {
        preg_match(self::TITLE_MATCH,$this->html,$title);
        return $title[count($title)-1];
    }

    /**
     * 视频简介
     * @return mixed
     */
    public function desc()
    {
        preg_match(self::DESC_MATCH,$this->html,$desc);
        return $desc[count($desc)-1];
    }

    /**
     * 上映时间
     * @return mixed
     */
    public function time()
    {
        preg_match( $this->type === "movie" ? self::TIME_MATCH : self::mTIME_MATCH, $this->html,$time);
        return $time[count($time)-1];
    }

    /**
     * 封面
     * @return mixed
     */
    public function cover()
    {
        preg_match_all(self::COVER_MATCH,$this->html,$cover);
        return $cover[count($cover)-2][0];
    }

    /**
     * 标签
     * @return mixed
     */
    public function tag()
    {
        preg_match_all(self::TAG_MATCH,$this->html,$tag);
        return $tag[count($tag)-1];
    }

    /**
     * 演员
     * @return false|string
     */
    public function actor()
    {
        preg_match_all(self::ACTOR_MATCH,$this->html,$actor);
        $tmp = [];
        foreach ($actor[2] as $key => $value) $tmp[] = [$actor[4][$key],$actor[2][$key]];
        return json_encode($tmp,true);
    }

    /**
     * 总集数
     * @return mixed
     */
    public function num()
    {
        preg_match(self::NUM_MATCH,$this->html,$num);
        return $num[count($num)-1];
    }

    /**
     * 每集信息
     */
    public function item()
    {
        preg_match_all(self::ITEM_MATCH,$this->html,$item);
        $tmp = [];
        foreach ($item[2] as $key => $value)
        {
            if (!strpos($item[3][$key],"预告")) $tmp[$value] = $item[1][$key];
            if (strpos($item[3][$key],"视频包月only-VIP"))
            {
                $tmp[$value]= $item[1][$key];
                unset($tmp[$key]);
            }
            elseif (strpos($item[3][$key],"超前点播"))
            {
                $tmp[$value]= $item[1][$key];
                unset($tmp[$key]);
            }
        }
        return $tmp;
    }
}