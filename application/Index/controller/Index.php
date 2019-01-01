<?php
namespace app\index\controller;

use app\admin\controller\BaseController;
use think\Db;

class Index extends BaseController
{
    public function index()
    {
       $data =  Db::name('admin')->where('id','2')->find();
        $this->assign('list',$data);
        return $this->fetch('index');
    }
}
