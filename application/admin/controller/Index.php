<?php
namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\model\UserBehavior;
use think\Db;

class Index extends BaseController
{
    public function index()
    {
        $uid = Admin::getCurAdminID();
        $last_login_time = (new UserBehavior())->getLastLoginRecord($uid,UserBehavior::USER_TYPE_ADMIN);
        $this->assign('last_login_time',$last_login_time);
        return $this->fetch('index/index');
    }
}
