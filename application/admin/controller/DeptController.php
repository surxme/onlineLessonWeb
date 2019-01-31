<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/31
 * Time: 10:54
 */

namespace app\admin\controller;

use app\admin\model\Dept;
use app\admin\model\Util;
use think\Db;

class DeptController extends BaseController
{
    /**
     * @return mixed
     */
    public function  index(){
        $params = input('param.');
        $list = (new Dept)->search($params);
        $this->assign('list',$list);
        return $this->fetch('dept/index');
    }

    /**
     *  添加课程
     * @throws \think\exception\DbException
     */
    public function add(){
        $id = input('param.id');
        if($id){
            $info = Dept::get($id);
            $this->assign('info',$info);
        }
        $dept = Dept::all();
        $this->assign('dept',$dept);

        return $this->fetch('dept/add');
    }

    /**
     * 保存
     * @return array
     */
    public function saveAdd(){
        $name = input('param.name');
        $id = input('param.id');
        $p_id = input('param.p_id');
        $data = ['name' => $name,'p_id' => $p_id];

        $dept = new Dept();
        if($id){
            $data['id'] = $id;
            $res = $dept->validate(true)->save($data,['id'=>$id]);
        }else{
            $res = $dept->validate(true)->save($data);
        }
        if($res){
            return Util::successArrayReturn();
        }else{
            return Util::errorArrayReturn(['msg'=>$dept->getError()]);
        }
    }

    /**
     * 删除
     */
    public function del(){
        $id=input('param.id');

        $lesson_count = Db::name('dept')->where(['p_id',$id])->count();
        if($lesson_count>0){
            return Util::errorArrayReturn(['msg' => '该部门下还包含子部门,暂不能删除']);
        }
        $re= (new Dept())->where('id',$id)->delete();
        if ($re){
            return Util::successArrayReturn();
        }else{
            return Util::errorArrayReturn();
        }
    }
}