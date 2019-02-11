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
//    public function details()
    {
//        $id = input('param.id',19);
        $id = 19;
        if(empty($id)){
            //错误操作
        }
        $data = Lesson::getOneLessonById($id);
        $suggestion = Db::name('lesson')->where('type_id',$data['type_id'])
            ->field('id,name,poster')->limit(5)
//            ->order('hits','desc')
            ->select();

        $this->assign('lesson',$data);
        $this->assign('suggestion',$suggestion);
        return $this->fetch('video-detail');
    }
}
