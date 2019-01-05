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

class LessonController extends BaseController
{
    public function  index(){
        $list = Db::table('t_lesson')->order('id desc')->paginate(10);

        $this->assign('list',$list);
        return $this->fetch('lesson/index');
    }

    /**
     *  添加课程
     */
    public function add(){
        $lesson_type = Type::all();
        $this->assign('lesson_type',$lesson_type);
        return $this->fetch('lesson/add')   ;
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

        $data = [
            'name' => $name,
            'type_id' => $tid,
            'intro' => $intro,
            'poster' => $poster
        ];

        $res = Lesson::create($data);

        if($res){
            return Util::successArrayReturn();
        }else{
            return Util::successArrayReturn();
        }
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
        $id=input('param.id');
        $re=Lesson::where('id',$id)->delete();
        if ($re){
            return Util::successArrayReturn();
        }else{
            return Util::errorArrayReturn();
        }
    }
}