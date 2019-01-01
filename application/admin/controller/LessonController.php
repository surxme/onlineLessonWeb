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
        $list = Db::table('t_lesson')->paginate(1);

        $this->assign('list',$list);
        return $this->fetch('lesson/index');
    }
}