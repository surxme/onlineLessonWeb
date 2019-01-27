<?php
namespace app\admin\model;

use think\Model;
use think\Session;

class Admin extends Model
{
    const USER_TYPE_STUDENT = 1;
    const USER_TYPE_TEACHER = 2;
    public static function getCurAdminID(){
        return Session::get('bigdata_admin_id');
    }

    public static function passwordfix($str){
        return md5(md5($str).'bigdata');
    }
}