<?php


namespace learn\services\mail;

use learn\utils\Mail;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * 邮件
 * Class MailService
 * @package learn\services\mail
 */
class MailService
{
    /**
     * 调试模式
     * @var int
     */
    protected static $debug = 1;

    /**
     * 编码格式
     * @var string
     */
    protected static $charSet = "UTF-8";

    /**
     * 实例
     * @var null
     */
    public static $instance = null;

    /**
     * @param array $config
     * @return null
     * @throws Exception
     */
    public static function init(array $config)
    {
        if ($config['type'] == 0)
        {
            (self::$instance === null) && (self::$instance = new PHPMailer(true));
            self::$instance->CharSet = self::$charSet;
            self::$instance->SMTPDebug = self::$debug;
            self::$instance->isSMTP();
            self::$instance->Host = $config['host'];
            self::$instance->SMTPAuth = true;
            self::$instance->Username = $config['username'];
            self::$instance->Password = $config['password'];
            self::$instance->SMTPSecure = 'ssl';
            self::$instance->Port = $config['port'];
            self::$instance->setFrom($config['from'],$config['from_name']);
            return self::$instance;
        }else if($config['type'] == 1)
        {
            (self::$instance === null) && (self::$instance = new Mail());
            self::$instance->host = $config['host'];
            self::$instance->password = $config['password'];
            self::$instance->mail_from = $config['username'];
            return self::$instance;
        }
    }

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function options()
    {
        $config = systemConfigMore(['mail_host','mail_username','mail_password','mail_port','mail_from','mail_from_name','mail_type']);
        return [
            'host' => $config['mail_host'],
            'username' => $config['mail_username'],
            'password' => $config['mail_password'],
            'port' => $config['mail_port'],
            'from' => $config['mail_from'],
            'from_name' => $config['mail_from_name'],
            'type' => $config['mail_type'],
        ];
    }

    /**
     * 邮件对象
     * @return MailService|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function mail()
    {
        self::init(self::options());
        return self::$instance;
    }

    /**
     * @return MailService
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function app()
    {
        self::mail();
        return new self();
    }

    /**
     * 接收者
     * @param string $to
     * @return null
     */
    public function to(string $to)
    {
        self::$instance->addAddress($to);
        return $this;
    }

    /**
     * 设置邮件类型
     * @param string $subtype
     * @return $this
     */
    public function subtype(string $subtype = "plain")
    {
        self::$instance->subtype($subtype);
        return $this;
    }

    /**
     * @param string $subject
     * @param string $body
     * @param string $altBody
     * @return bool
     */
    public function send(string $subject, string $body,string $altBody = "邮件客户端不支持HTML")
    {
        try {
            self::$instance->Subject = $subject;
            self::$instance->Body    = $body;
            self::$instance->AltBody = $altBody;
            self::$instance->send();
            return true;
        }catch (Exception $e)
        {
            var_dump($e->errorMessage());
            return false;
        }
    }
}