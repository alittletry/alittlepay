<?php


namespace learn\services\crawler;

use learn\utils\Curl;

/**
 * 360看视频
 * Class KanService
 * @package learn\services\crawler
 */
class KanService
{
    /**
     * 实例
     * @var null
     */
    private static $instance = null;

    /**
     * 视频类型
     * @var null
     */
    public $type = "movie";

    /**
     * @var string
     */
    public $url = "https://www.360kan.com";

    /**
     * 电影视频地址
     * @var
     */
    const M_URL = "https://www.360kan.com/m/";

    /**
     * 电影视频地址
     * @var
     */
    const TV_URL = "https://www.360kan.com/tv/";

    /**
     * 动漫视频地址
     * @var
     */
    const D_URL = "https://www.360kan.com/ct/";

    /**
     * 综艺视频地址
     * @var
     */
    const Z_URL = "https://www.360kan.com/va/";

    /**
     * 标题
     */
    const TITLE_MATCH = "/<h1>([^<>]+)<\/h1>/";

    /**
     * 简介
     */
    const DESC_MATCH = "/<p class=\"item-desc\">([^<>]+)<\/p>/";

    /**
     * 上映时间
     */
    const TIME_MATCH = "/<p class=\"item\"><span>年代 ：<\/span>([^<>]+)<\/p>/";

    /**
     * 出品时间-电视剧
     */
    const mTIME_MATCH = "/<p class=\"item\"><span>年代 ：<\/span>([^<>]+)<\/p>/";
    /**
     * 封面
     */
    const COVER_MATCH = "/<a href=\"([^<>]+)\" class=\"g-playicon s-cover-img\" data-daochu=\"([^<>]+)\" data-num=\"([^<>]+)\">        <img src=\"([^<>]+)\">            (.*?)<\/a>/";

    /**
     * 标签
     */
    const TAG_MATCH = "/<a class=\"cat(.*?)\" href=\"([^<>]+)\" target=\"_blank\" monitor-shortpv-c-sub=\"([^<>]+)\">([^<>]+)<\/a>/";

    /**
     * 演员
     */
    const ACTOR_MATCH = "/<a class=\"name\" href=\"([^<>]+)\">([^<>]+)<\/a>/";

    /**
     * 总集数
     */
    const NUM_MATCH = "/<p class=\"tag\">更新至<span>([^<>]+)<\/span>\/共([^<>]+)集 <\/p>/";

    /**
     * 总集数
     */
    const NUM2_MATCH = "/<p class=\"tag\">全<span>([^<>]+)<\/span>集<\/p>/";

    /**
     * 每集信息
     */
    const ITEM_MATCH = "/<a data-num=\"([^<>]+)\" data-daochu=\"([^<>]+)\" href=\"([^<>]+)\">            ([^<>]+)            (.*?)<\/a>/";

    /**
     * 资源类型
     */
    const SOURCE_MATCH = "/<a href=\"([^<>]+)\" class=\"g-playicon s-cover-img\" data-daochu=\"to=([^<>]+)\" data-num=\"1\"     monitor-shortpv-c-sub=\"([^<>]+)\">/";

    /**
     * 视频地址
     */
    const URL_MATCH = "/<a href=\"([^<>]+)\" class=\"g-playicon s-cover-img\" data-daochu=\"to=([^<>]+)\" data-num=\"1\"     monitor-shortpv-c-sub=\"([^<>]+)\">/";

    /**
     * QQService constructor.
     * @param string $vid
     * @param string|null $type
     */
    public function __construct(string $vid, string $type = null)
    {
        if ($type !== null) $this->type = $type;
        if ($type == "tv")
        {
            $this->url = self::TV_URL.$vid.".html";
        }elseif ($type == "movie")
        {
            $this->url = self::M_URL.$vid.".html";
        }elseif ($type == "dm")
        {
            $this->url = self::D_URL.$vid.".html";
        }elseif ($type == "zy")
        {
            $this->url = self::Z_URL.$vid.".html";
        }
    }

    /**
     * @param string $vid
     * @param string|null $type
     * @return QQService|null
     */
    public static function app(string $vid,string $type = null)
    {
        self::$instance = new self($vid,$type);
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
            $source = self::source();
            $url = self::url();
            switch ($this->type)
            {
                case "movie":
                    return compact("title","desc","time","cover","tag","actor","source","url");
                case "tv":
                case "dm":
                case "zy":
                    $num = self::num();
                    $item = self::item();
                    $now_num = count($item);
                    return compact("title","desc","time","cover","tag","actor","num","item","now_num","source","url");
            }
        }catch (\Exception $e)
        {

        }
    }

    /**
     * 视频地址
     * @return mixed
     */
    public function url()
    {
        preg_match(self::URL_MATCH,$this->html,$source);
        return $source[count($source)-2];
    }

    /**
     * 资源类型
     * @return mixed
     */
    public function source()
    {
        preg_match(self::SOURCE_MATCH,$this->html,$source);
        return $source[count($source)-2];
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
        foreach ($actor[count($actor)-1] as $key => $value) $tmp[] = [$value,""];
        return json_encode($tmp,true);
    }

    /**
     * 总集数
     * @return mixed
     */
    public function num()
    {
        preg_match(self::NUM_MATCH,$this->html,$num);
        if (empty($num))
        {
            preg_match(self::NUM2_MATCH,$this->html,$num);
            $this->now_num = $num[count($num)-1];
            return $num[count($num)-1];
        }
        $this->now_num = $num[count($num)-2];
        return $num[count($num)-1];
    }

    /**
     * 每集信息
     */
    public function item()
    {
        preg_match_all(self::ITEM_MATCH,$this->html,$item);
        $tmp = [];
        for ($i=1;$i<$this->now_num+1;$i++) $tmp[$i] = $item[3][array_search($i, $item[4])];
        return $tmp;
    }
}