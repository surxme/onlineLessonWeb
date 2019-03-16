<?php
namespace app\index\controller;

use app\admin\model\Util;
use app\index\model\Comment;
use app\index\model\CommentReply;
use app\index\model\Lesson;
use app\index\model\Notice;
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
        $reply = Db::name('comment_reply')
            ->where('data_id',$question['id'])
            ->where('type',Comment::TYPE_QUESTION)
            ->where('floor_id',0)
            ->order('id desc')
            ->paginate(10)
            ->each(function ($item,$key){
                if($item['user_type'] == UserBehavior::USER_TYPE_STUDENT){
                    $user = Student::get($item['uid']);
                }else{
                    $user = Teacher::get($item['uid']);
                }
                $item['comment_uname'] = $user['name'];
                $item['comment_uname_avatar'] = $user['avatar'];

                if($item['receive_user_type'] == UserBehavior::USER_TYPE_STUDENT){
                    $receive_user = Student::get($item['receive_uid']);
                }else{
                    $receive_user = Teacher::get($item['receive_uid']);
                }
                $item['comment_receive_uname'] = $receive_user['name'];
                $item['comment_receive_uname_avatar'] = $receive_user['avatar'];

                //楼层下的子回复
                $child_reply = Db::name('comment_reply')
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

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function saveQuestionReplay(){
        //{content:content,comment_id:comment_id,floor_id:floor_id,question_id:question_id},
        if(empty($this->_cur_user)){
            return Util::errorArrayReturn(['msg'=>'暂未登录']);
        }

        $content = input('param.content');
        $comment_id = input('param.comment_id');
        $floor_id = input('param.floor_id');
        $question_id = input('param.question_id');

        $comment = ['uid'=>0,'user_type'=>1];
        //被回复的评论
        if($comment_id){
            $comment = Db::name('comment_reply')->where('id',$comment_id)->find();
        }
        if($comment_id==0){
            $comment = Db::name('comment')->where('id',$question_id)->find();
        }
        $receive_uid = $comment['uid'];
        $receive_user_type = $comment['user_type'];

        $insert_data = [
            'data_id' => $question_id,
            'comment_id' => $comment_id,
            'uid' => $this->_cur_user['id'],
            'user_type' => $this->_cur_user['u_type'],
            'receive_uid' => $receive_uid,
            'receive_user_type' => $receive_user_type,
            'content' => $content,
            'floor_id' => $floor_id,
            'type' => Comment::TYPE_QUESTION,
        ];

        $comment_reply = new CommentReply();
        $res = $comment_reply->validate(true)->save($insert_data);

        if(!$res){
            return Util::errorArrayReturn(['msg'=>$comment_reply->getError()]);
        }
        //通知信息
        $notice_data = [
            'uid' => $this->_cur_user['id'],
            'user_type' => $this->_cur_user['u_type'],
            'type' => Notice::TYPE_REPLY,
            'receive_id' => $receive_uid,
            'receive_user_type' => $receive_user_type,
            'detail_id' => $comment_reply->getLastInsID(),
        ];

        //如果是二级楼层回复有楼层id直接显示该楼层下以及下面的二级信息
        //如果没有就直接显示从该楼开始的offset后面的所有回复信息
        $notice = new Notice();
        $notice->data($notice_data)->save();

        return Util::successArrayReturn();
    }
}
