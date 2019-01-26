<?php
namespace app\admin\model;

use think\Model;
use think\Session;

class Admin extends Model
{
    public static function getCurAdminID(){
        return Session::get('bigdata_admin_id');
    }

    public static function passwordfix($str){
        return md5(md5($str).'bigdata');
    }
}