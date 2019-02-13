<?php
namespace app\index\controller;

use app\index\model\Lesson;
use think\Controller;
use think\Db;

class Index extends Controller
{
    /**
     * 首页
     * @return mixed
     */
    public function index()
    {
        $params = input('param.');
        $list = (new Lesson())->search($params);
        $this->assign('list',$list);
        return $this->fetch('index');
    }

    /**
     * 课程详情
     * @return mixed
     */
    public function details()
    {
        $video_id = input('param.video_id',0);
        $id = input('param.id');
        $data = Lesson::getOneLessonById($id,$video_id);

        $this->assign('lesson',$data['lesson']);
        $this->assign('video_list',$data['video_list']);
        $this->assign('video',$data['video']);
        $this->assign('suggestion',$data['suggestion']);
        return $this->fetch('details');
    }
}
