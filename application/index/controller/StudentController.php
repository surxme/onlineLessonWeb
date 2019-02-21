<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/2/19
 * Time: 21:41
 */

namespace app\index\controller;


use app\admin\model\Util;
use app\index\model\Admin;
use app\index\model\Comment;
use app\index\model\Curriculum;
use app\index\model\Subscribe;
use app\index\model\UserBehavior;
use think\Db;

class StudentController extends BaseController
{
    public $uid = 0;
    public $u_type = 0;
    protected function _initialize()
    {
        list($uid,$type) = Admin::getCurUserID();
        if(!$uid||!$type){
            $this->redirect('index/index');
        }
        $this->uid = $uid;
        $this->u_type = $type;
        parent::_initialize();
    }

    public function index(){
        $userBehavior = new UserBehavior();
        $last_login_time = $userBehavior->getLastLoginRecord($this->uid,UserBehavior::USER_TYPE_STUDENT,UserBehavior::ACTION_TYPE_LOGIN);
        $this->assign('login_time',$last_login_time);
        return $this->fetch('student/index');
    }

    /**
     * 我的课程表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function curriculum(){
        $curriculum = new Curriculum();

        $list = $curriculum->where(['t.student_id'=>$this->uid])->alias('t')
            ->join('lesson','t.lesson_id = lesson.id')
            ->field('lesson.id as lesson_id,t.id,t.create_time,lesson.name,lesson.poster')
            ->paginate(10);
        $this->assign('list',$list);

        return $this->fetch('student/curriculum');
    }

    /**
     * 删除我的课程
     * @return array
     */
    public function curriculumDel(){
        $id = input('param.id');

        $curriculum = new Curriculum();
        $res = $curriculum->where('id',$id)->delete();

        if($res){
            return Util::successArrayReturn(['msg'=>'移除成功']);
        }else{
            return Util::errorArrayReturn(['msg'=>'移除失败']);
        }
    }

    /**
     * 获取我的评论
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function studentComment(){
        $list = Db::name('comment')->alias('t')->join('video v','t.data_id = v.id')
            ->join('lesson l','v.lesson_id = l.id')
            ->field('t.content,t.id as comment_id,l.id as lesson_id,l.name as lesson_name,v.id as video_id,v.name as video_name,l.poster,t.create_time')
            ->where('t.uid',$this->uid)
            ->where('t.type',Comment::TYPE_COMMENT)
            ->paginate(10);

        $this->assign('list',$list);

        return $this->fetch('student/comment');
    }

    /**
     * 获取我的评论
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function studentQuestion(){
        $list = Db::name('comment')->alias('t')->join('video v','t.data_id = v.id')
            ->join('lesson l','v.lesson_id = l.id')
            ->field('t.content,t.id as comment_id,l.id as lesson_id,
                        l.name as lesson_name,v.id as video_id,v.name as video_name,
                        l.poster,t.create_time,t.title as title')
            ->where('t.uid',$this->uid)
            ->where('t.type',Comment::TYPE_QUESTION)
            ->paginate(10);

        $this->assign('list',$list);

        return $this->fetch('student/question');
    }

    /**
     * 获取我的关注
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function subscribe(){
        $list = Db::name('subscribe')->alias('t')
            ->join('teacher tea','t.teacher_id = tea.id')
            ->field('tea.name as teacher_name,tea.avatar as avatar,t.teacher_id')
            ->where('t.uid',$this->uid)
            ->where('t.u_type ',$this->u_type)
            ->paginate(10)->each(function($item, $key){
                $item['subscribe_num'] = Subscribe::where('teacher_id',$item['teacher_id'])->count();
                return $item;
            }
        );

        $this->assign('list',$list);

        return $this->fetch('student/subscribe');
    }
}