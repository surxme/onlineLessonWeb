<?php
namespace app\index\controller;

use app\index\model\Comment;
use app\index\model\Lesson;
use think\Db;

class Question extends BaseController
{
    /**
     * 问答详情
     * @return mixed
     */
    public function index(){
        $params = input('param.id');
        $list = (new Lesson())->search($params);
        $this->assign('list', $list);
        return $this->fetch('index');
    }

    public function detail(){
        $id = input('param.id');

        //问题
        $question = Db::name('comment')->where(['id'=>$id,'type'=>Comment::TYPE_QUESTION])->find();

        $breadcrumbs = Db::name('video')
            ->alias('t')
            ->join('lesson','t.lesson_id = lesson.id')
            ->where('t.id',$question['data_id'])
            ->field('t.id as video_id,t.name as video_name,lesson.id as lesson_id,lesson.name as lesson_name')
            ->find();
        //楼主回复
        $reply = Db::name('comment')
            ->where('data_id',$id)
            ->where('type',Comment::TYPE_QUESTION)
            ->where('floor_id',$id)
            ->select();

        $this->assign('question',$question);
        $this->assign('breadcrumbs',$breadcrumbs);

        return $this->fetch('index/questiondetail');
    }
}
