<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/1
 * Time: 20:54
 */

namespace app\admin\controller;

use app\admin\model\Dept;
use app\admin\model\Lesson;
use app\admin\model\Teacher;
use app\admin\model\Type;
use app\admin\model\Util;
use think\Db;

class LessonController extends BaseController
{
    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function  index(){
        $params = input('param.');
        $list = (new Lesson())->search($params);

        $this->assign('list',$list);
        return $this->fetch('lesson/index');
    }

    /**
     *  添加课程
     * @throws \think\exception\DbException
     */
    public function add(){
        $id = input('param.id');
        if($id){
            $info = Lesson::get($id);
            $this->assign('info',$info);
            $dept = Dept::getDept(explode(',',$info['teacher_ids']));
        }else{
            $dept = Dept::getDept();
        }
        $lesson_type = Type::all();

        $this->assign('dept',json_encode($dept));
        $this->assign('lesson_type',$lesson_type);
        return $this->fetch('lesson/add');
    }

    /**
     * 保存课程
     * @return array
     */
    public function saveAdd(){
        $name = input('param.name');
        $tid = input('param.tid');
        $intro = input('param.intro');
        $poster = input('param.poster');
        $teacher_ids = input('param.teacher_ids');
        $id = input('param.id');

        $data = [
            'name' => $name,
            'type_id' => $tid,
            'intro' => $intro,
            'poster' => $poster,
            'teacher_ids' => $teacher_ids
        ];

        $lesson = new Lesson();

        if($id){
            $res = $lesson->validate(true)->save($data,['id'=>$id]);
        }else{
            $res = $lesson->validate(true)->save($data);
        }

        if($res){
            return Util::successArrayReturn();
        }else{
            return Util::errorArrayReturn(['msg'=>$lesson->getError()]);
        }
    }

    /**
     * 删除课程
     */
    public function del(){
        $id=input('param.id');
        $re= (new Lesson)->where('id',$id)->delete();
        if ($re){
            return Util::successArrayReturn();
        }else{
            return Util::errorArrayReturn();
        }
    }

    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function detail(){
        $id = input('param.id');
        $info = Lesson::get($id);
        $this->assign('info',$info);

        return $this->fetch('lesson/detail')   ;
    }
}