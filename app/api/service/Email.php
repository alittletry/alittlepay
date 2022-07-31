<?php
namespace app\api\service;

use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    public function send($content,$title='通知')
    {
        $toemail = systemConfig("notify_email");
        $mail = new PHPMailer();
        // 使用SMTP服务
        $mail->isSMTP();
        // 编码格式为utf8，不设置编码的话，中文会出现乱码
        $mail->CharSet = "utf8";
        // 发送人的SMTP服务器地址（QQ邮箱就是“smtp.qq.com”）
        $mail->Host = systemConfig("mail_host");
        // 是否使用身份验证
        $mail->SMTPAuth = true;
        // 发送人的邮箱用户名，就是你自己的SMTP服务使用的邮箱
        $mail->Username = systemConfig("mail_username");
        // 发送方的邮箱密码，注意这里填写的是“客户端授权密码”而不是邮箱的登录密码！
        $mail->Password = systemConfig("mail_password");
        // 使用ssl协议方式
        $mail->SMTPSecure = "ssl";
        //ssl协议方式端口号是465
        $mail->Port = systemConfig("mail_port");
 
        // 设置发件人信息，如邮件格式说明中的发件人，这里会显示为  Mailer(xxx@qq.com）
        $mail->setFrom(systemConfig("mail_from"),systemConfig("mail_from_name"));
        // 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
        $mail->addAddress($toemail, 'Notice');
        // 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址
        $mail->addReplyTo(systemConfig("mail_from"),systemConfig("mail_from_name"));
 
        //$mail->addCC("xxx@163.com");// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址(这个人也能收到邮件)
        //$mail->addBCC("xxx@163.com");// 设置秘密抄送人(这个人也能收到邮件)
        //$mail->addAttachment("bug0.jpg");// 添加附件
 
        // 邮件标题
        $mail->Subject = $title;
        // 邮件正文
        $mail->Body = $content;
        //$mail->AltBody = "This is the plain text纯文本";// 这个是设置纯文本方式显示的正文内容，如果不支持Html方式，就会用到这个，基本无用
        if (!$mail->send()) {
            // 发送邮件
            
            //echo "Mailer Error: " . $mail->ErrorInfo;// 输出错误信息
           
            return ['code'=>201,'msg'=>'发送失败','data'=>$mail->ErrorInfo];
        } else {
            return ['code'=>200,'msg'=>'发送成功'];
        }

    }
}
