<?php
namespace app\index\controller;

use app\index\model\Admin;
use app\index\model\Comment;
use app\index\model\Lesson;
use app\index\model\Student;
use app\index\model\Teacher;
use app\index\model\UserBehavior;
use think\Controller;
use think\Db;

class Index extends Controller
{
    /**
     * 首页
     * @return mixed
     */
    public function index(){
        $params = input('param.');
        $list = (new Lesson())->search($params);
        $this->assign('list',$list);
        return $this->fetch('index');
    }

    /**
     * 课程详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function details(){
        $video_id = input('param.video_id',0);
        $id = input('param.id');
        $data = Lesson::getOneLessonById($id,$video_id);

        $with_teachers = Db::name('lesson_attr')->alias('t')->join('t_teacher tt','t.teacher_id = tt.id','left')
            ->where('t.lesson_id',$id)->field('tt.id as teacher_id,tt.avatar,tt.name')->select();

        $this->assign('lesson',$data['lesson']);
        $this->assign('video_list',$data['video_list']);
        $this->assign('video',$data['video']);
        $this->assign('suggestion',$data['suggestion']);
        $this->assign('with_teachers',$with_teachers);
        return $this->fetch('details');
    }

    /**
     * 获取当前查看的视频的评论/问答信息 1评论 2问答
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function getCommentsById(){
        $video_id = input('param.id');
        $type = input('param.type');
        $comments = Comment::search($type,$video_id);

        list($cur_uid,$user_type) = Admin::getCurUserID();

        $user = [];
        if($user_type==UserBehavior::USER_TYPE_STUDENT){
            $user = Student::get($cur_uid);
        }

        $this->assign('comques',$comments);
        $this->assign('type',$type);
        $this->assign('user',$user);
        $this->assign('user_type',$user_type);

        return $this->fetch('comment');
    }

    public function saveComment(){

    }
}
