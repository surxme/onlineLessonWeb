<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/1
 * Time: 20:54
 */

namespace app\admin\controller;

use think\Db;

class LessonController extends BaseController
{
    public function  index(){
        $list = Db::table('t_lesson')->paginate(10);

        $this->assign('list',$list);
        return $this->fetch('lesson/index');
    }

    /**
     *  添加课程
     */
    public function add(){
        return $this->fetch('lesson/add')   ;
    }

    /**
     * 编辑课程
     */
    public function edit(){
        return $this->fetch('lesson/edit');
    }

    /**
     * 删除课程
     */
    public function del(){

    }
}