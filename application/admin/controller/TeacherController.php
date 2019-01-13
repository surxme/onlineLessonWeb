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
        $params = input('param.');

        $list = (new Teacher())->search($params);

        $dept = Dept::getDeptTree();
        $this->assign('list',$list);
        $this->assign('dept',json_encode($dept));
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
        $sex = input('param.sex');
        $bir = input('param.bir');
        $avatar = input('param.avatar');
        $email = input('param.email');
        $dept_id = input('param.dept_id');
        $id = input('param.id');

        $data = [
            'name' => $name,
            'sex' => $sex,
            'bir' => strtotime($bir),
            'avatar' => $avatar,
            'email' => $email,
            'dept_id' => $dept_id
        ];

        $teacher = new Teacher();
        // 调用当前模型对应的User验证器类进行数据验证
        if($id){
            $res = $teacher->validate(true)->save($data,['id'=>$id]);
        }else{
            $data['password'] = md5(md5('123456').'bigdata');
            $res = $teacher->validate(true)->save($data);
        }

        if($res){
            return Util::successArrayReturn();
        }else{
            return Util::errorArrayReturn(['msg'=>$teacher->getError()]);
        }
    }

    /**
     * 删除课程
     */
    /**
     * @return array
     */
    public function del(){
        $id=input('param.id');
        $re = Teacher::update(['is_del'=>1],['id'=>$id]);
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