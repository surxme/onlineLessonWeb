<?php
namespace app\index\controller;

use app\admin\model\Util;
use app\index\model\Admin;
use app\index\model\Comment;
use app\index\model\Curriculum;
use app\index\model\Lesson;
use app\index\model\Notice;
use app\index\model\Student;
use app\index\model\Subscribe;
use app\index\model\UserBehavior;
use app\index\model\VideoAttr;
use think\Db;

class Index extends BaseController
{
    /**
     * 首页
     * @return mixed
     */
    public function index(){
        $params = input('param.');
        $params['order'] = input('param.order',1);
        $params['ltype'] = input('param.ltype','');
        $list = (new Lesson())->search($params);
        $this->assign('params',$params);
        $this->assign('list',$list);
        return $this->fetch('index');
    }

    /**
     * 点击教师名进入教师个人详情界面
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function teacherHome(){
        $teacher_id = input('teacher_id');
        if(!$teacher_id){
            $this->redirect('index/index');
        }

        $teacher = Db::name('teacher')->alias('t')
            ->join('dept','t.dept_id = dept.id','LEFT')
            ->where('t.id',$teacher_id)
            ->field('t.id,t.name,t.bir,t.sex,t.avatar,t.email,dept.name as dept_name')
            ->find();

        $params = [
            'teacher_id' => $teacher_id
        ];

        $lesson = new Lesson($params);
        $list = $lesson->search();
        $this->assign('list',$list);
        $this->assign('teacher',$teacher);

        return $this->fetch('index/teacherhome');
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
        $is_check = input('param.is_check');
        $notice_id = input('param.notice_id',0);

        if($notice_id&&$is_check == 0){
            Notice::where('id', $notice_id)
                ->update(['status' => '1']);
        }

        $data = Lesson::getOneLessonById($id,$video_id);
        list($uid,$user_type) = Admin::getCurUserID();

        //当前课程下所有视频的观看量、点赞量求和
        $views = Db::name('lesson')->alias('t')->join('video','t.id = video.lesson_id')
            ->field('sum(video.hits) as hits,sum(video.thumbs) as thumbs')->group('video.lesson_id')->where('t.id',$id)->find();

        //当前课程关联的教师
        $with_teachers = Db::name('lesson_attr')->alias('t')->join('t_teacher tt','t.teacher_id = tt.id','left')
            ->where('t.lesson_id',$id)->field('tt.id as teacher_id,tt.avatar,tt.name')->select();

        //是否对当前访问的视频点赞过
        $is_thumbs_uped = 0;
        if($uid) {
            $video_attr = new VideoAttr();
            $va_db_obj = $video_attr->where('video_id', $data['video']['id'])->where('uid', $uid)->where('type', 1);
            $is_thumbs_uped = $va_db_obj->find();
        }

        //是否关注发布当前视频的教师
        $is_subscribed = 0;
        if($uid) {
            $subscribe = new Subscribe();
            $subscribe_obj = $subscribe->where('teacher_id', $data['video']['teacher_id'])->where('uid', $uid)->where('u_type', $user_type);
            $is_subscribed = $subscribe_obj->find();
        }

        //附件
        $attachment = json_decode($data['video']['attachment'],true);

        $this->assign('lesson',$data['lesson']);
        $this->assign('video_list',$data['video_list']);
        $this->assign('video',$data['video']);
        $this->assign('suggestion',$data['suggestion']);
        $this->assign('user_type',$user_type);
        $this->assign('with_teachers',$with_teachers);
        $this->assign('hits',$views['hits']);
        $this->assign('thumbs',$views['thumbs']);
        $this->assign('is_thumbs_uped',$is_thumbs_uped?1:0);
        $this->assign('attachment',$attachment);
        $this->assign('is_subscribed',$is_subscribed?1:0);
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

    /**
     * 保存评论/问答
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function saveComment(){
        $uid = Admin::getCurStudentID();
        $type = input('param.type');
        $title = input('param.title');
        $content = input('param.content');
        $user_type = input('param.user_type');
        $video_id = input('param.data_id');
        $comment = new Comment();

        $user = Student::get($uid);
        if($user['is_banned']){
            return Util::errorArrayReturn(['msg'=>'您已被禁言,请联系管理员解除']);
        }

        $msg = '操作失败，请重试';
        $data = [
            'uid'=>$uid,
            'type'=>$type,
            'content'=>$content,
            'user_type'=>$user_type,
            'data_id'=>$video_id
        ];
        $is_exist = 0;
        if($uid){
            if($type==Comment::TYPE_QUESTION){
                $data['title']=$title;
            }else{
                $is_exist = $comment->where(['data_id'=>$video_id,'type'=>Comment::TYPE_COMMENT,'uid'=>$uid])->find();
            }
            if($is_exist){
                $msg = '您已经评论过该视频了';
            }else{
                $res = $comment->validate(true)->save($data);
                if($res){
                    return Util::successArrayReturn();
                }else{
                    return Util::errorArrayReturn(['msg'=>$comment->getError()]);
                }
            }
        }else{
            $msg = '暂无已登录账号';
        }
        return Util::errorArrayReturn(['msg'=>$msg]);
    }

    /**
     * 删除评论
     * @return array
     * @throws \think\exception\DbException
     */
    public function commentDel(){
        $id=input('param.id');

        $comment = Comment::get($id);

        list($uid,$user_type) = Admin::getCurUserID();

        if($comment['uid']!=$uid){
            return Util::errorArrayReturn(['msg'=>'参数错误']);
        }

        $re= (new Comment())->where('id',$id)->delete();
        if ($re){
            return Util::successArrayReturn(['msg'=>'删除成功']);
        }else{
            return Util::errorArrayReturn(['msg'=>'删除失败']);
        }
    }

    /**
     * 视频点赞保存
     * @return array
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function thumbsUpSave(){
        $id = input('param.id');
        $type= input('param.type');
        list($uid,$user_type) = Admin::getCurUserID();

        if(!$uid){
            return Util::errorArrayReturn(['msg'=>'暂无已登录账号']);
        }

        $video_attr = new VideoAttr();
        $va_db_obj = $video_attr->where('video_id',$id)->where('uid',$uid)->where('type',$type);
        $v_db_obj = Db::name('video')->where('id',$id);
        $is_exist = $va_db_obj->find();
        $save_res = 0;

        $msg = '取消赞';
        $code = 1;//取消

        Db::startTrans();

        $data = [
            'uid'=>$uid,
            'user_type'=>$user_type,
            'type'=>$type,
            'video_id'=>$id,
        ];

        //已经点赞
        if($is_exist){
            $res = $va_db_obj->delete($is_exist['id']);
            if($res) {
                $save_res = $v_db_obj->setDec('thumbs');
            }
        }else{
            $res = $video_attr->save($data);
            if($res){
                $save_res = $v_db_obj->setInc('thumbs');
                $msg='点赞';
                $code = 2;
            }
        }
        if($save_res){
            Db::commit();
            return Util::successArrayReturn(['msg'=>$msg,'code'=>$code]);
        }else{
            Db::rollback();
            return Util::errorArrayReturn(['msg'=>$msg,'code'=>$code]);
        }
    }

    /**
     * 添加课程到我的课程表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function curriculumSave(){
        $id = input('param.id');
        $uid = Admin::getCurStudentID();

        if(!$uid){
            return Util::successArrayReturn(['msg'=>'暂无已登录账号']);
        }

        $curriculum = new Curriculum();
        $curriculum_db_obj = $curriculum->where('lesson_id',$id)->where('student_id',$uid);
        $is_exist = $curriculum_db_obj->find();

        $data = [
            'lesson_id'=>$id,
            'student_id'=>$uid
        ];

        if($is_exist){
            return Util::errorArrayReturn(['msg'=>'课程表中已存在该课程']);
        }else{
            $res = $curriculum->save($data);
            if($res){
                return Util::successArrayReturn(['msg'=>'添加成功，可前往课程表中查询']);
            }else{
                return Util::errorArrayReturn(['msg'=>'添加失败，请稍后重试']);
            }
        }
    }

    /**
     * 视频观看锚点 点击播放视频后需要记录用户的操作锚点（登录前提）
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function watchVideoAnchor(){
        $id = input('param.id');
        list($uid,$user_type) = Admin::getCurUserID();

        if($uid){
            $data = [
                'user_type'=>$user_type,
                'uid'=>$uid,
                'action_type'=>UserBehavior::ACTION_TYPE_WATCH_VIDEO
            ];
            $is_execute = 0;
            $last_time = UserBehavior::getLastActionTime($data,$id)['create_time'];
            if($last_time){
                $time_range = time()-(int)$last_time-1800;
                if($time_range>0){
                    $is_execute = 1;
                }
            }else{
                $is_execute = 1;
            }

            if($is_execute){
                Db::startTrans();
                $v_db_obj = Db::name('video')->where('id',$id);
                $save_res = 0;
                $res = UserBehavior::insertBehavior($data,$id);
                //已经点赞
                if($res) {
                    $save_res = $v_db_obj->setInc('hits');
                }
                if($save_res){
                    Db::commit();
                }else{
                    Db::rollback();
                }
            }
        }
    }

    /**
     * 点击订阅/关注 如果已经关注则取消关注
     * @return array
     */
    public function saveSubscribe(){
        $teacher_id = input('param.teacher_id');

        if(!$this->_cur_user){
            return Util::errorArrayReturn(['msg' => '无法获取已登录用户,请重新登录再试']);
        }
        $uid = $this->_cur_user['id'];
        $u_type = $this->_cur_user['u_type'];

        $data = [
            'teacher_id' => (int)$teacher_id,
            'uid' => $uid,
            'u_type' =>$u_type
        ];
        $subscribe = new Subscribe();

        $exist_id = $subscribe->where($data)->value('id');

        $code = 1;
        $msg = '关注成功';
        if($exist_id){
            $res = $subscribe->where('id',$exist_id)->delete();
            $msg = '已取消关注';
            $code = 2;
        }else{
            $res = $subscribe->validate(true)->save($data);
        }

        if($res){
            return Util::successArrayReturn(['msg' => $msg , 'code' => $code]);
        }else{
            return Util::errorArrayReturn(['msg' => $subscribe->getError(), 'code' => $code]);
        }
    }
}
