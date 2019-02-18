<?php
namespace app\index\controller;

use app\index\model\Admin;
use think\Controller;

class BaseController extends Controller
{
    /**
     * @throws \think\exception\DbException
     */
    protected function _initialize()
    {
        $user = Admin::getCurUserInfo();
        $this->assign('curUserInfo',$user);
    }

    public function _empty(){
        $this->redirect('index/index');
    }
}