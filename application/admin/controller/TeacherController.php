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

class TeacherController extends BaseController
{
    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function  index(){
        $list = Db::name('teacher')->order('id desc')->paginate(10);
        $this->assign('list',$list);
        return $this->fetch('teacher/index');
    }

    /**
     *  添加课程
     * @throws \think\exception\DbException
     */
    public function add(){
        $id = input('param.id');
        if($id){
            $info = Teacher::get($id);
            $this->assign('info',$info);
        }

        $dept = Dept::all();
        $this->assign('dept',$dept);

        return $this->fetch('teacher/add');
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

        if($id){
            $res = Teacher::update($data,['id'=>$id]);
        }else{
            $res = Teacher::create($data);
        }

        if($res){
            return Util::successArrayReturn();
        }else{
            return Util::successArrayReturn();
        }
    }

    /**
     * 删除课程
     */
    public function del(){
        $id=input('param.id');
        $re= (new Teacher)->where('id',$id)->delete();
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
        $info = Teacher::get($id);
        $this->assign('info',$info);

        return $this->fetch('teacher/detail')   ;
    }
}