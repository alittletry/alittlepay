<?php


namespace learn\utils;

/**
 * Class Curl
 * @package learn\utils
 */
class Curl
{
    /**
     * 请求url
     * @var string
     */
    public $url;

    /**
     * 参数
     * @var null
     */
    public $params;

    /**
     * type
     * post get
     * @var string
     */
    public $type;

    /**
     * 请求头
     * @var array
     */
    public $header = array(
        "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.125 Safari/537.36",
        "Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9"
        );

    /**
     * Curl constructor.
     * @param string $url
     * @param string $type
     * @param string $params
     */
    public function __construct(string $url, string $type = 'GET', $params = "")
    {
        $this->url = $url;
        $this->type = $type;
        $this->params = $params;
    }

    /**
     * @param string $url
     * @param string $type
     * @param array $params
     * @return Curl
     */
    public static function app(string $url, string $type = 'GET', $params = "")
    {
        return new self($url, $type, $params);
    }

    /**
     * 设置请求头
     * @param $header
     * @return Curl
     */
    public function header($header)
    {
        $this->header = $header;
        return $this;
    }

    /**
     * GET 请求
     * @return bool|string
     */
    public function get()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($curl);
        if (!curl_error($curl)) {curl_close($curl);return $data;}
        else print "Error: " . curl_error($curl);
    }

    /**
     * POST 请求
     * @return bool|string
     */
    public function post()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE );
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->params);
        $data = curl_exec($curl);
        if (!curl_error($curl)) {curl_close($curl);return $data;}
        else print "Error: " . curl_error($curl);
    }

    /**
     * 自动运行
     * @return bool|string
     */
    public function run()
    {
        switch ($this->type)
        {
            case 'GET':
                return $this->get();
            case 'POST':
                return $this->post();
        }
    }

    /**
     * 生成sign
     * @param string $client_secret
     */
    public function buildSign(string $client_secret)
    {
        $sign = $client_secret;
        ksort($this->params);
        foreach ($this->params as $k=>$v) $sign .=$k.$v;
        $sign.=$client_secret;
        $this->params['sign'] = strtoupper(md5($sign));
        return $this;
    }
}
