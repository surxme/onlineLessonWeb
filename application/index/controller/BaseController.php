<?php
namespace app\index\controller;

use app\index\model\Admin;
use think\Controller;
use think\Db;

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

        $lessonType = Db::name('type')->select();

        $this->assign('curUserInfo',$user);
        $this->assign('lessonType',$lessonType);
    }

//    public function _empty(){
//        $this->redirect('index/index');
//    }
}