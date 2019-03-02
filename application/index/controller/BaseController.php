<?php
namespace app\index\controller;

use app\index\model\Admin;
use think\Controller;

class BaseController extends Controller
{
    protected $_cur_user = 0;
    /**
     * @throws \think\exception\DbException
     */
    protected function _initialize()
    {
        $user = Admin::getCurUserInfo();
        $this->_cur_user = $user;
        $this->assign('curUserInfo',$user);
    }

    public function _empty(){
        $this->redirect('index/index');
    }
}