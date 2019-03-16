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
use app\index\model\Lesson;
use app\index\model\Notice;
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

    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(){
        $userBehavior = new UserBehavior();
        $last_login_time = $userBehavior->getLastLoginRecord($this->uid,UserBehavior::USER_TYPE_TEACHER,UserBehavior::ACTION_TYPE_LOGIN);

        $start = mktime(0, 0, 0, date('m'), date('d') - 6, date('Y'));
        $end = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;

        //最近7天点击量
        list($xAxis_name_arr1,$echart_data_increment1) = Teacher::getEchartData($start,$end,$this->uid,'hits','观看量');
//        $this->assign('xAxis_name_arr',$xAxis_name_arr1);
//        $this->assign('echart_data_increment',$echart_data_increment1);
        //最近7天点赞量
        list($xAxis_name_arr2,$echart_data_increment2) = Teacher::getEchartData($start,$end,$this->uid,'thumbs','点赞量');
        $xAxis_name_arr = [$xAxis_name_arr1,$xAxis_name_arr2];
        $echart_data_increment = [$echart_data_increment1,$echart_data_increment2];

        $this->assign('xAxis_name_arr',json_encode($xAxis_name_arr1));
        $this->assign('echart_data_increment',json_encode($echart_data_increment));




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
    public function videoAlt(){
        $id = input('param.id');

        $video = [];
        if($id){
            $video = Video::get($id);
            $video['attachment_str'] = Video::implodeAttachments($video['attachment']);
            $video['attachment_init_obj'] = Video::initAttachmentData($video['attachment']);
            $video['path_str'] = Video::implodeAttachments($video['path']);
            $video['path_init_obj'] = Video::initAttachmentData($video['path']);
//            $video['attachment'] = json_decode($video['attachment'],true);
        }

        $lesson = new Lesson();
        $my_lesson = $lesson->alias('t')->join('lesson_attr la','t.id = la.lesson_id')
            ->where('la.teacher_id',$this->uid)->field('t.id,t.name')->select();

        $this->assign('lesson',$my_lesson);
        $this->assign('info',$video);

        return $this->fetch('teacher/videoAlt');
    }

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function videoSaveAlt(){
        $data = input('param.');
        $id = input('param.id');

        if(!$this->uid){
            return Util::errorArrayReturn(['msg' => '暂无已登录用户']);
        }

        $data['teacher_id'] = $this->uid;
        $attachments = input('param.attachment');
        $video_file = input('param.path');

        $attach_data = [];
        if ($attachments){
            $attach_data = Video::explodeAttachments($attachments);
        }
        $data['attachment'] = json_encode($attach_data);
        $video_data = Video::explodeAttachments($video_file);
        $data['path'] = json_encode($video_data);

        $video = new Video();

        if($id){
            $data['id'] = $id;
            $res = $video->validate(true)->save($data,['id'=>$id]);
        }else{
            $res = $video->validate(true)->save($data);
        }

        if(!$id){
            //关注课程的人
            $insert_data = [];
            $detail_id = $video->getLastInsID();

            $curriculum_users = Db::name('curriculum')->where('lesson_id',$data['lesson_id'])->select();
            foreach ($curriculum_users as $c_user){
                $insert_data[] = [
                    'uid' => $this->uid,
                    'user_type' => $this->u_type,
                    'type' => Notice::TYPE_NEW_LESSON,
                    'receive_id' => $c_user['student_id'],
                    'receive_user_type' => UserBehavior::USER_TYPE_STUDENT,
                    'detail_id' => $detail_id
                ];
            }

            $subscribe_users = Db::name('subscribe')->where('teacher_id',$this->uid)->select();
            foreach ($subscribe_users as $s_user){
                $insert_data[] = [
                    'uid' => $this->uid,
                    'user_type' => $this->u_type,
                    'type' => Notice::TYPE_NEW_LESSON,
                    'receive_id' => $s_user['uid'],
                    'receive_user_type' => $s_user['u_type'],
                    'detail_id' => $detail_id
                ];
            }
            //如果是二级楼层回复有楼层id直接显示该楼层下以及下面的二级信息
            //如果没有就直接显示从该楼开始的offset后面的所有回复信息
            $insert_data = array_unique($insert_data,SORT_REGULAR);
            $notice = new Notice();
            $notice->saveAll($insert_data);
        }

        if($res){
            return Util::successArrayReturn(['msg'=>'操作成功']);
        }else{
            return Util::errorArrayReturn(['msg'=>$video->getError()]);
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
            ->where('t.u_type ',UserBehavior::USER_TYPE_TEACHER)
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