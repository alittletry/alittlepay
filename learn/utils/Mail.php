<?php


namespace learn\utils;

/**
 * 宝塔邮件发送
 * Class Mail
 * @package learn\utils
 */
class Mail
{
    /**
     * 邮件服务器地址
     * @var null
     */
    public $host = null;

    /**
     * 发件人邮箱地址
     * @var null
     */
    public $mail_from = null;

    /**
     * 发件人邮箱密码
     * @var null
     */
    public $password = null;

    /**
     * 收件人，多个逗号隔开
     * @var null
     */
    public $mail_to = null;

    /**
     * 邮件类型
     * @var string
     */
    public $subtype = "plain";

    /**
     * 主题
     * @var string
     */
    public $Subject = "";

    /**
     * 内容
     * @var string
     */
    public $Body = "";

    /**
     * @param string $to
     * @return $this
     */
    public function addAddress(string $to)
    {
        $this->mail_to = $to;
        return $this;
    }

    /**
     * @param string $subtype
     * @return $this
     */
    public function subtype(string $subtype)
    {
        $this->subtype = $subtype;
        return $this;
    }

    /**
     * @return Curl
     */
    public function send()
    {
        $param = [
            'mail_from' => $this->mail_from,
            'password' => $this->password,
            'mail_to' => $this->mail_to,
            'subject' => $this->Subject,
            'content' => $this->Body,
            'subtype' => $this->subtype,
        ];
        return Curl::app($this->host,"POST",$param)->run();
    }
}