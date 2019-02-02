<?php
namespace app\index\controller;

use app\index\model\Lesson;
use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
        $params = input('param.');
        $list = (new Lesson())->search($params);
        $this->assign('list',$list);
        return $this->fetch('index');
    }

    public function test()
    {
        $data =  Db::name('admin')->where('id','2')->find();
        $this->assign('list',$data);
        return $this->fetch('video');
    }
}
