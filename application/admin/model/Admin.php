<?php
namespace app\admin\model;

use think\Model;
use think\Session;

class Admin extends Model
{
    public static function getCurAdminID(){
        return Session::get('bigdata_admin_id');
    }
}