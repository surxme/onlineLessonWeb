<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
       $data =  Db::name('lesson')->paginate(10);
        $this->assign('list',$data);
        return $this->fetch('index');
    }

    public function test()
    {
        $data =  Db::name('admin')->where('id','2')->find();
        $this->assign('list',$data);
        return $this->fetch('video');
    }
}
