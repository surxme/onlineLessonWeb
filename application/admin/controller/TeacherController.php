<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/26
 * Time: 20:54
 */

namespace app\admin\controller;

use app\admin\model\Dept;
use app\admin\model\Teacher;
use app\admin\model\Util;
use think\Db;

class TeacherController extends BaseController
{
    /**
     * @return mixed
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
     *  添加
     * @throws \think\exception\DbException
     */
    public function add(){
        $id = input('param.id');
        if($id){
            $info = Db::name('teacher')->where('id',$id)->find();
            $this->assign('info',$info);
        }

        $dept = Dept::all();
        $this->assign('dept',$dept);

        return $this->fetch('teacher/add');
    }

    /**
     * 保存
     * @return array
     */
    public function saveAdd(){
        $name = input('param.name');
        $teacher_no = input('param.teacher_no');
        $sex = input('param.sex');
        $bir = input('param.bir');
        $avatar = input('param.avatar');
        $email = input('param.email');
        $dept_id = input('param.dept_id');
        $id = input('param.id');

        $data = [
            'name' => $name,
            'teacher_no' => $teacher_no,
            'sex' => $sex,
            'bir' => strtotime($bir),
            'avatar' => $avatar,
            'email' => $email,
            'dept_id' => $dept_id
        ];

        $teacher = new Teacher();
        // 调用当前模型对应的User验证器类进行数据验证
        if($id){
            $data['id'] = $id;
            $res = $teacher->validate(true)->save($data,['id'=>$id]);
        }else{
            $data['password'] = md5(md5('t123456').'bigdata');
            $res = $teacher->validate(true)->save($data);
        }

        if($res){
            return Util::successArrayReturn();
        }else{
            return Util::errorArrayReturn(['msg'=>$teacher->getError()]);
        }
    }

    /**
     * 删除
     */
    /**
     * @return array
     */
    public function del(){
        $id=input('param.id');
        Db::startTrans();
        try{
            Teacher::destroy($id);
            Teacher::update(['is_del'=>1],['id'=>$id]);
            // 提交事务
            Db::commit();
            return Util::successArrayReturn();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
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

    /**
     * @throws \think\exception\DbException
     */
    public function export(){
        $list = Db::name('teacher')->alias('t')->order('t.id desc')->join('t_dept dept','t.dept_id = dept.id','LEFT')
        ->field('t.name,t.teacher_no,t.sex,t.email,dept.name as dept_name,t.bir')->select();

        foreach ($list as $k => $item){
            $list[$k]['sex'] = $item['sex']==1?'男':'女';
            $list[$k]['bir'] = date('Y-m-d',$item['bir']);
        }

        $list=[
            'list'=>$list,
            'title'=>['姓名','职工号','性别','邮箱','部门','生日']
        ];

        return $list;
    }

    public function import(){
        return $this->fetch('teacher/importchoose');
    }
}