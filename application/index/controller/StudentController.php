<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/2/19
 * Time: 21:41
 */

namespace app\index\controller;


use app\index\model\Admin;
use app\index\model\Curriculum;
use app\index\model\UserBehavior;

class StudentController extends BaseController
{
    public $uid = 0;
    protected function _initialize()
    {
        list($uid,$type) = Admin::getCurUserID();
        if(!$uid||!$type){
            $this->redirect('index/index');
        }
        $this->uid = $uid;
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
            ->field('t.create_time,lesson.name,lesson.poster')
            ->paginate(10);
        $this->assign('list',$list);

        return $this->fetch('student/curriculum');
    }
}