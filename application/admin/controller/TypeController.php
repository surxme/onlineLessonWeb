<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/1
 * Time: 20:54
 */

namespace app\admin\controller;

use app\admin\model\Lesson;
use app\admin\model\Type;
use app\admin\model\Util;
use think\Db;

class TypeController extends BaseController
{
    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function  index(){
        $params = input('param.');
        $list = (new Type)->search($params);
        $this->assign('list',$list);
        return $this->fetch('type/index');
    }

    /**
     *  添加课程
     * @throws \think\exception\DbException
     */
    public function add(){
        $id = input('param.id');
        if($id){
            $info = Type::get($id);
            $this->assign('info',$info);
        }

        return $this->fetch('type/add');
    }

    /**
     * 保存课程
     * @return array
     */
    public function saveAdd(){
        $name = input('param.name');
        $id = input('param.id');
        $data = ['name' => $name];

        $type = new Type();
        if($id){
            $res = $type->validate(true)->save($data,['id'=>$id]);
        }else{
            $res = $type->validate(true)->save($data);
        }
        if($res){
            return Util::successArrayReturn();
        }else{
            return Util::errorArrayReturn(['msg'=>$type->getError()]);
        }
    }

    /**
     * 删除课程
     */
    public function del(){
        $id=input('param.id');

        $lesson_count = Db::name('lesson')->where(['type_id',$id])->count();
        if($lesson_count>0){
            return Util::errorArrayReturn(['msg' => '该类别还包含有课程，暂不能删除']);
        }
        $re= (new Type())->where('id',$id)->delete();
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
        $info = Type::get($id);
        $this->assign('info',$info);

        return $this->fetch('type/detail')   ;
    }
}