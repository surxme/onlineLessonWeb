<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/3/6
 * Time: 23:48
 */

namespace app\index\controller;


use app\admin\model\Util;
use app\index\model\Admin;
use app\index\model\Student;
use app\index\model\Teacher;
use app\index\model\UserBehavior;
use app\index\model\VerifyCode;
use think\Controller;
use think\Db;
use think\Validate;

class RegisterController extends Controller
{
    public function index(){
        return $this->fetch('login/register');
    }

    public function registerSave(){
        $verfiy_code = input('param.verifycode');

        $student_no = input('param.student_no');
        $name = input('param.name');
        $email = input('param.email');
        $pass = input('param.pass');
        $confirmpass = input('param.confirmpass');

        if($pass!==$confirmpass){
            return Util::errorArrayReturn(['msg'=>'确认密码不一致']);
        }

        $code = Db::name('verify_code')->where('email',$email)->order('id desc')->value('code');

        if(!$code){
            return Util::errorArrayReturn(['msg'=>'请先获取验证码']);
        }

        if($code !== $verfiy_code){
            return Util::errorArrayReturn(['msg'=>'验证码错误，请重试']);
        }

        $data = [
            'student_no' => $student_no,
            'name' => $name,
            'email' => $email,
            'password' => Admin::passwordfix($pass),
            'bir' => strtotime(date('Y-m-d')),
            'sex' => 1,
            'avatar' => DS . 'uploads' . DS . 'poster'.DS.'logo.svg'
        ];

        $student = new Student();

        $res = $student->validate(true)->save($data);

        if($res){
            return Util::successArrayReturn(['msg' => '注册成功']);
        }else{
            return Util::errorArrayReturn(['msg' => $student->getError()]);
        }
    }

    public function forget(){
        return $this->fetch('login/forget');
    }

    public function resetPass(){
        $verfiy_code = input('param.verifycode');
        $u_type = input('param.u_type');
        $email = input('param.email');
        $pass = input('param.pass');
        $confirmpass = input('param.confirmpass');

        if($pass!==$confirmpass){
            return Util::errorArrayReturn(['msg'=>'确认密码不一致']);
        }

        $code = Db::name('verify_code')->where('email',$email)->order('id desc')->find();
        $time_range = time()-(int)$code['create_time'];
        if(!$code){
            return Util::errorArrayReturn(['msg'=>'请先获取验证码']);
        }
        if($time_range > 300){
            return Util::errorArrayReturn(['msg'=>'验证码过期，请重新获取']);
        }
        if($code['code'] != $verfiy_code){
            echo gettype($code['code']).' '.gettype($verfiy_code);
            return Util::errorArrayReturn(['msg'=>'验证码错误，请重试']);
        }

        if($pass){
            $validate = new Validate([
                'password'  => 'min:6|max:12',
            ],[
                'password.min' => '密码为6-12位',
                'password.max' => '密码为6-12位',
            ]);
            if (!$validate->check(['password' => $pass])) {
                return Util::errorArrayReturn(['msg' => $validate->getError()]);
            }
        }

        $data = [
            'password' => Admin::passwordfix($pass),
        ];

        if(!in_array($u_type,[UserBehavior::USER_TYPE_STUDENT,UserBehavior::USER_TYPE_TEACHER])){
            return Util::errorArrayReturn(['msg'=>'未知错误']);
        }
        if($u_type == UserBehavior::USER_TYPE_STUDENT){
            $user = new Student();
        }else{
            $user = new Teacher();
        }
        $res = $user->save($data,['email'=>$email]);

        if($res){
            return Util::successArrayReturn(['msg' => '修改成功']);
        }else{
            return Util::errorArrayReturn(['msg' => $user->getError()]);
        }
    }
    
    public function test(){
        $this->error('aaaaa');
//        return $this->fetch('exception_html/404');
    }
}