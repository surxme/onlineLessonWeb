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
use app\index\model\Lesson;
use app\index\model\Student;
use app\index\model\Subscribe;
use app\index\model\Teacher;
use app\index\model\UserBehavior;
use app\index\model\Video;
use think\Db;
use think\Validate;

class TeacherController extends BaseController
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
        $last_login_time = $userBehavior->getLastLoginRecord($this->uid,UserBehavior::USER_TYPE_TEACHER,UserBehavior::ACTION_TYPE_LOGIN);
        $this->assign('login_time',$last_login_time);
        return $this->fetch('teacher/index');
    }

    /**
     * 我的课程表
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function schedule(){
        $list = Db::name('lesson_attr')->alias('t')->join('lesson','lesson.id = t.lesson_id')
        ->where('t.teacher_id',$this->uid)->paginate(10);

        $this->assign('list',$list);

        return $this->fetch('teacher/schedule');
    }

    /**
     * 我的视频
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function videos(){
        $list = Db::name('video')->alias('t')->join('lesson','lesson.id = t.lesson_id')
            ->field('t.name as video_name,t.id as video_id,t.create_time,lesson.name as lesson_name,t.lesson_id,lesson.poster')
        ->where('t.teacher_id',$this->uid)->paginate(10);

        $this->assign('list',$list);

        return $this->fetch('teacher/videos');
    }

    /**
     * 添加/修改课程
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function videoAdd(){
        $lesson = new Lesson();
        $my_lesson = $lesson->alias('t')->join('lesson_attr la','t.id = la.lesson_id')
            ->where('la.teacher_id',$this->uid)->field('t.id,t.name')->select();
        $this->assign('lesson',$my_lesson);
        return $this->fetch('teacher/videoAdd');
    }

    public function videoSaveAdd(){
        $data = input('param.');

        if(!$this->uid){
            return Util::errorArrayReturn(['msg' => '暂无已登录用户']);
        }

        $data['teacher_id'] = $this->uid;
        $video = new Video();

        $res = $video->data($data)->save();
        if($res){
            return Util::successArrayReturn(['msg'=>'操作成功']);
        }else{
            return Util::errorArrayReturn(['msg'=>'操作失败']);
        }
    }

    /**
     * 删除我的视频
     * @return array
     */
    public function videoDel(){
        $id = input('param.id');

        $video = new Video();
        $res = $video->where('id',$id)->where('teacher_id',$this->uid)->delete();

        if($res){
            return Util::successArrayReturn(['msg'=>'移除成功']);
        }else{
            return Util::errorArrayReturn(['msg'=>'移除失败']);
        }
    }

    /**
     * 获取关于我的评论
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function teacherComment(){
        $list = Db::name('comment')->alias('t')->join('video v','t.data_id = v.id')
            ->join('lesson l','v.lesson_id = l.id')
            ->join('lesson_attr la','la.lesson_id = l.id')
            ->field('t.content,t.id as comment_id,l.id as lesson_id,l.name as lesson_name,v.id as video_id,v.name as video_name,l.poster,t.create_time')
            ->where('la.teacher_id',$this->uid)
            ->where('t.type',Comment::TYPE_COMMENT)
            ->paginate(10);

        $this->assign('list',$list);

        return $this->fetch('teacher/comment');
    }

    /**
     * 获取关于我的问答
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function teacherQuestion(){
        $list = Db::name('comment')->alias('t')->join('video v','t.data_id = v.id')
            ->join('lesson l','v.lesson_id = l.id')
            ->join('lesson_attr la','la.lesson_id = l.id')
            ->field('t.content,t.id as comment_id,l.id as lesson_id,
                        l.name as lesson_name,v.id as video_id,v.name as video_name,
                        l.poster,t.create_time,t.title as title')
            ->where('la.teacher_id',$this->uid)
            ->where('t.type',Comment::TYPE_QUESTION)
            ->paginate(10);

        $this->assign('list',$list);

        return $this->fetch('teacher/question');
    }

    /**
     * 获取我的关注
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function subscribe(){
        $list = Db::name('subscribe')->alias('t')
            ->join('teacher tea','t.teacher_id = tea.id')
            ->field('t.id,tea.name as teacher_name,tea.avatar as avatar,t.teacher_id')
            ->where('t.uid',$this->uid)
            ->where('t.u_type ',$this->u_type)
            ->paginate(10)->each(function($item, $key){
                $item['subscribe_num'] = Subscribe::where('teacher_id',$item['teacher_id'])->count();
                return $item;
            }
        );

        $this->assign('list',$list);

        return $this->fetch('teacher/subscribe');
    }

    /**
     * 删除我的订阅
     * @return array
     */
    public function subscribeDel(){
        $id = input('param.id');

        $subscribe = new Subscribe();
        $res = $subscribe->where('id',$id)->where('uid',$this->uid)->where('u_type',$this->u_type)->delete();

        if($res){
            return Util::successArrayReturn(['msg'=>'移除成功']);
        }else{
            return Util::errorArrayReturn(['msg'=>'移除失败']);
        }
    }

    /**
     * 学生个人资料
     * @throws \think\exception\DbException
     */
    public function profile(){
        $info = Teacher::get($this->uid);

        $this->assign('info',$info);

        return $this->fetch('teacher/profile');
    }

    /**
     * 保存
     * @return array
     */
    public function profileSave(){
        $name = input('param.name');
        $teacher_no = input('param.teacher_no');
        $sex = input('param.sex');
        $bir = input('param.bir');
        $avatar = input('param.avatar');
        $email = input('param.email');
        $id = input('param.id');
        $password = input('param.password');

        $data = [
            'name' => $name,
            'teacher_no' => $teacher_no,
            'sex' => $sex,
            'bir' => strtotime($bir),
            'avatar' => $avatar,
            'email' => $email,
        ];

        if($password){
            $validate = new Validate([
                'password'  => 'min:6|max:12',
            ],[
                'password.min' => '密码为6-12位',
                'password.max' => '密码为6-12位',
            ]);
            if (!$validate->check(['password' => $password])) {
                return Util::errorArrayReturn(['msg' => $validate->getError()]);
            }
            $data['password'] = Admin::passwordfix($password);
        }

        $teacher = new Teacher();
        // 调用当前模型对应的User验证器类进行数据验证
        if($id){
            $data['id'] = $id;
            $res = $teacher->validate(true)->save($data,['id'=>$id]);
        }else{
            return Util::errorArrayReturn(['msg'=>'参数错误']);
        }

        if($res){
            return Util::successArrayReturn();
        }else{
            return Util::errorArrayReturn(['msg'=>$teacher->getError()]);
        }
    }
}