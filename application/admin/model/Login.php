<?php
namespace app\admin\model;

use think\Db;
use think\Model;
use think\Session;

class Login extends Model
{
	public function login($name,$pass){
		$admin=Db::name("admin")->where("name",$name)->find();
        if(!empty($admin)){
            if($this->getMd5Password($pass)!=$admin['password']){
                return 1;
            }else{
                Session::set('bigdata_admin_name',$name);
                Session::set('bigdata_admin_id',$admin['id']);
                Session::set('bigdata_admin_is_super',$admin['is_super']);
                return 3;
            }
        }else{
            return 2;
        }
	}

	public function getMd5Password($str){
        return md5(md5($str).'bigdata');
    }
}