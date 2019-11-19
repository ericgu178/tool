<?php
namespace tool;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * 邮件服务
 *
 * @author EricGU178
 */
class Mail extends Base 
{
    public $mail = [
        'SMTP_HOST'   => 'smtp.163.com', //SMTP服务器
        'SMTP_PORT'   => '465', //SMTP服务器端口
        'SMTP_USER'   => '', //SMTP服务器用户名
        'SMTP_PASS'   => '', //SMTP服务器密码
        'FROM_EMAIL'  => '', //发件人EMAIL
        'FROM_NAME'   => '', //发件人名称
        'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
        'REPLY_NAME'  => '', //回复名称（留空则为发件人名称）
        'RECEIVE_EMAIL'  => '', //接收的邮箱
    ];

    /**
     * 发送邮箱用户
     *
     * @var array
     * @author EricGU178
     */
    public $to = [
        'ericgu178@gmail.com'   =>  'gxj',
    ];
    
    /**
     * 初始化
     *
     * @author EricGU178
     */
    public function __construct($config)
    {
        if (empty($config)) {
            throw new \Exception('请填写邮箱配置');
        }
        $this->mail['SMTP_HOST'] = $config['SMTP_HOST'] ?? $this->mail['SMTP_HOST'];
        $this->mail['SMTP_PORT'] = $config['SMTP_PORT'] ?? $this->mail['SMTP_PORT'];
        $this->mail['SMTP_USER'] = $config['SMTP_USER'] ?? \trigger_error('请填写SMTP服务器用户名');
        $this->mail['SMTP_PASS'] = $config['SMTP_PASS'] ?? \trigger_error('请填写SMTP服务器密码');
        $this->mail['FROM_EMAIL'] = $config['FROM_EMAIL'] ?? \trigger_error('请填写发件人EMAIL');
        $this->mail['FROM_NAME'] = $config['FROM_NAME'] ?? \trigger_error('请填写发件人名称');
        $this->mail['FROM_NAME'] = $config['FROM_NAME'] ?? \trigger_error('请填写发件人名称');
    }

    /**
     * 发送邮件
     *
     * @param array $to 收件人
     * @param string $subject 标题
     * @param string $body 内容
     * @param null $attachment 附件
     * @return void
     * @author EricGU178
     */
    public function sendEmail($to, $subject = 'test', $body = '', $attachment = null)
    {
        $mail             = new PHPMailer(); //PHPMailer对象
        $mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP();  // 设定使用SMTP服务
        $mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能
                                                   // 1 = errors and messages
                                                   // 2 = messages only
        $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
        $mail->SMTPSecure = 'ssl';                 // 使用安全协议
        $mail->Host       = $this->mail['SMTP_HOST'];  // SMTP 服务器
        $mail->Port       = $this->mail['SMTP_PORT'];  // SMTP服务器的端口号
        $mail->Username   = $this->mail['SMTP_USER'];  // SMTP服务器用户名
        $mail->Password   = $this->mail['SMTP_PASS'];  // SMTP服务器密码
        $mail->SetFrom($this->mail['FROM_EMAIL'], $this->mail['FROM_NAME']);
        $replyEmail       = $this->mail['REPLY_EMAIL'] ? $this->mail['REPLY_EMAIL'] : $this->mail['FROM_EMAIL'];
        $replyName        = $this->mail['REPLY_NAME'] ? $this->mail['REPLY_NAME'] : $this->mail['FROM_NAME'];
        $mail->AddReplyTo($replyEmail, $replyName);
        $mail->Subject    = $subject;
        $mail->MsgHTML($body);
        foreach ($to as $key => $value) {
            $mail->AddAddress($key, $value);
        }
        if(is_array($attachment)){ // 添加附件
            foreach ($attachment as $file){
                is_file($file) && $mail->AddAttachment($file);
            }
        }
        return $mail->Send() ? true : $mail->ErrorInfo;
    }
}