<?php

namespace app\index\controller;

use app\admin\model\Util;
use app\index\model\Admin;
use app\index\model\Login;
use app\index\model\UserBehavior;
use think\Controller;
use think\Session;

class LoginController extends Controller
{
    public function login(){
        if(Session::has('bigdata_user_type')){
            $this->redirect('index/index');
        }
        return $this->fetch('login/login1');
    }

    public function loginVerify(){
        $name=input('param.name');
        $pass=input('param.password');
        $user_type=input('param.type');

        if(!in_array($user_type,[1,2])){
            return Util::errorArrayReturn(['msg'=>'参数错误']);
        }
        $login=new Login();
        $result=$login->login($name,$pass,$user_type);
        if($result==1){
            return Util::errorArrayReturn(['msg'=>'密码或账号错误']);
        }else if($result==2){
            return Util::errorArrayReturn(['msg'=>'账号不存在']);
        }else{
            list($uid,$aa) = Admin::getCurUserID();
            (new UserBehavior())->insertBehavior(['user_type' => $user_type,'uid' => $uid,'action_type' => UserBehavior::ACTION_TYPE_LOGIN]);
            return Util::successArrayReturn(['msg'=>'登录成功']);
        }
    }

    public function logout(){
        Session::delete('bigdata_teacher_id');
        Session::delete('bigdata_student_id');
        Session::delete('bigdata_user_type');
        Session::delete('bigdata_user_name');
        $this->redirect('index/index');
    }
}
