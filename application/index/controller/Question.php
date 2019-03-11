<?php
namespace app\index\controller;

use app\index\model\Comment;
use app\index\model\Lesson;
use app\index\model\Student;
use app\index\model\Teacher;
use app\index\model\UserBehavior;
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
        $question = Db::name('comment')
            ->alias('t')
            ->join('student','t.uid = student.id')
            ->where(['t.id'=>$id,'t.type'=>Comment::TYPE_QUESTION])
            ->field('t.*,student.name as student_name,student.avatar as student_avatar')
            ->find();

        $breadcrumbs = Db::name('video')
            ->alias('t')
            ->join('lesson','t.lesson_id = lesson.id')
            ->where('t.id',$question['data_id'])
            ->field('t.id as video_id,t.name as video_name,lesson.id as lesson_id,lesson.name as lesson_name')
            ->find();
        //楼主回复
        //列表中显示的是被回复data_id = video_id and comment_id = 0 and floor_id = 0的，表示贴主发布

        //点击进入帖子后，显示的是 data_id = question_id and and floor_id = 0 回复帖子的楼层主回复

        //楼层下面显示的是 data_id = question_id and comment_id = 楼层id floor_id = 楼层id


        //所有回复楼层
        $reply = Db::name('comment_replay')
            ->where('data_id',$question['id'])
            ->where('type',Comment::TYPE_QUESTION)
            ->where('floor_id',0)
            ->paginate(10)
            ->each(function ($item,$key){
                if($item['user_type'] == UserBehavior::USER_TYPE_STUDENT){
                    $user = Student::get($item['uid']);
                }else{
                    $user = Teacher::get($item['uid']);
                }
                $item['comment_uname'] = $user['name'];
                $item['comment_uname_avatar'] = $user['avatar'];

                //楼层下的子回复
                $child_reply = Db::name('comment_replay')
                    ->where('data_id',$item['data_id'])
                    ->where('floor_id',$item['id'])
                    ->where('type',Comment::TYPE_QUESTION)
                    ->select();
                $child_reply_bak = $child_reply;
                foreach ($child_reply_bak as $k => $child){
                    if($child['user_type'] == UserBehavior::USER_TYPE_STUDENT){
                        $user = Student::get($child['uid']);
                    }else{
                        $user = Teacher::get($child['uid']);
                    }
                    $child_reply[$k]['comment_uname'] = $user['name'];
                    $child_reply[$k]['comment_uname_avatar'] = $user['avatar'];

                    if($item['receive_user_type'] == UserBehavior::USER_TYPE_STUDENT){
                        $receive_user = Student::get($child['receive_uid']);
                    }else{
                        $receive_user = Teacher::get($child['receive_uid']);
                    }
                    $child_reply[$k]['comment_receive_uname'] = $receive_user['name'];
                    $child_reply[$k]['comment_receive_uname_avatar'] = $receive_user['avatar'];
                }
                $item['child'] = $child_reply;
                return $item;
            });

        $this->assign('breadcrumbs',$breadcrumbs);
        $this->assign('reply',$reply);
        $this->assign('question',$question);

        return $this->fetch('index/questiondetail');
    }
}
