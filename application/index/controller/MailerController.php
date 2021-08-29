<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/3/9
 * Time: 13:15
 */

namespace app\index\controller;
use app\admin\model\Util;
use app\index\model\UserBehavior;
use app\index\model\VerifyCode;
use PHPMailer\PHPMailer\PHPMailer;

use think\Controller;
use think\Db;
use think\Validate;

class MailerController extends Controller
{
    /**
     * @return array
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(){
        //1.查询邮箱是否可用
        //2.发送邮件
        //3.写入数据
        //4.等待验证
        $email = input('email');

        $validate = new Validate([
            'email' => 'email'
        ]);
        $verify_data = [
            'email' => $email
        ];
        if (!$validate->check($verify_data)) {
            return Util::errorArrayReturn(['msg'=>'邮箱格式有误']);
        }
        //是否被使用
        $is_be_used = Db::name('student')->where(['email'=>$email])->find();
        if($is_be_used){
            return Util::errorArrayReturn(['msg'=>'该邮箱已被使用']);
        }
        $code = $this->createSMSCode();
        $data = [
            'user_email' => $email,  //接收人邮箱
            'content' => 'BIGDATA，您的验证码为：'.$code.',请勿回复。'
        ];
        $success = $this->sendEmail($data);
        $verify = new VerifyCode();
        $verify_data = [
            'email' => $email,
            'code' => $code
        ];

        if(!$success){
            return Util::errorArrayReturn(['msg'=>'服务器错误，发送失败']);
        }
        $verify->data($verify_data)->save();

        return Util::successArrayReturn(['msg'=>'发送成功，请到邮箱查看']);
    }

    /**
     * 随机生成6位验证码
     * @param int $length
     * @return int
     */
    public function createSMSCode($length = 6){
        $min = pow(10 , ($length - 1));
        $max = pow(10, $length) - 1;
        return rand($min, $max);
    }

    /**
     * 发送验证码邮件
     * @param array $data
     * @return int
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public  function sendEmail($data = []) {
        $mail = new phpmailer(); //实例化
        $mail->IsSMTP(); // 启用SMTP
        $mail->Host = 'smtp.qq.com'; //SMTP服务器 以qq邮箱为例子
        $mail->Port = 465;  //邮件发送端口
        $mail->SMTPAuth = true;  //启用SMTP认证
        $mail->SMTPSecure = "ssl";   // 设置安全验证方式为ssl
        $mail->CharSet = "UTF-8"; //字符集
        $mail->Encoding = "base64"; //编码方式
        $mail->Username = '446634431@qq.com';  //发件人邮箱
        $mail->Password = 'muquxuzlfcxwbjgb';  //发件人密码 ==>重点：是授权码，不是邮箱密码
        $mail->Subject = 'BIGDATA验证'; //邮件标题
        $mail->From = '446634431@qq.com';  //发件人邮箱
        $mail->FromName = 'BIGDATA';  //发件人姓名
        if($data && is_array($data)){
            $mail->AddAddress($data['user_email']); //添加收件人
            $mail->IsHTML(true); //支持html格式内容
            $mail->Body = $data['content']; //邮件主体内容
            //发送成功就删除
            if ($mail->Send()) {
                //echo "Mailer Error: ".$mail->ErrorInfo;// 输出错误信息,用以邮件发送不成功问题排查
                return 1;
            }else{
                return -1;
            }

        }
        return 1;
    }


    /**
     * @return array
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sendForget(){
        $email = input('email');
        $u_type = input('u_type');

        $validate = new Validate([
            'email' => 'email'
        ]);
        $verify_data = [
            'email' => $email
        ];
        if (!$validate->check($verify_data)) {
            return Util::errorArrayReturn(['msg'=>'邮箱格式有误']);
        }
        $table = 'student';
        if($u_type == UserBehavior::USER_TYPE_TEACHER){
            $table = 'teacher';
        }
        //是否存在该用户
        $is_be_used = Db::name($table)->where(['email'=>$email])->find();
        if(!$is_be_used){
            return Util::errorArrayReturn(['msg'=>'没有查询到该邮箱关联的账号']);
        }
        $code = $this->createSMSCode();
        $data = [
            'user_email' => $email,  //接收人邮箱
            'content' => 'BIGDATA，您的验证码为：'.$code.',五分钟内有效，请勿回复。'
        ];
        $success = $this->sendEmail($data);
        $verify = new VerifyCode();
        $verify_data = [
            'email' => $email,
            'code' => $code
        ];

        if(!$success){
            return Util::errorArrayReturn(['msg'=>'服务器错误，发送失败']);
        }
        $verify->data($verify_data)->save();

        return Util::successArrayReturn(['msg'=>'发送成功，请到邮箱查看']);
    }

}