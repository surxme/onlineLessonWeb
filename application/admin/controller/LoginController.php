<?php

namespace app\admin\controller;

use app\admin\model\Login;
use think\captcha\Captcha;
use think\Controller;
use think\Session;

class LoginController extends Controller
{
    public function login(){
        return $this->fetch('login/login');
    }

    public function loginVerify(){
        $name=input('name');
        $pass=input('password');
        $login=new Login();
        $result=$login->login($name,$pass);
        if($result==1){
            $result = [
                'status' => 'error',
                'msg' => '密码错误！'
            ];
        }else if($result==2){
            $result = [
                'status' => 'error',
                'msg' => '用户不存在！'
            ];
        }else{
            $result = [
                'status' => 'ok',
                'msg' => '登录成功'
            ];
        }
        return $result;
    }

    public function logout(){
        Session::delete('admin_id');
        $this->redirect('admin/login');
    }
}
