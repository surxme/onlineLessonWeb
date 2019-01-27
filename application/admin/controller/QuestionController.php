<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/27
 * Time: 20:54
 */

namespace app\admin\controller;

use app\admin\model\Question;
use app\admin\model\Student;
use app\admin\model\Util;

class QuestionController extends BaseController
{
    /**
     * @return mixed
     */
    public function  index(){
        $params = input('param.');
        $list = (new Question())->search($params);
        $this->assign('list',$list);
        return $this->fetch('comment/index');
    }

    /**
     * 删除
     * @return array
     */
    public function del(){
        $id=input('param.id');
        $re= (new Question())->where('id',$id)->save(['is_del'=>1]);
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
    public function ban(){
        $id = input('param.id');
        $info = Question::get($id);

        $res = Student::update(['is_banned'=>1],['id'=>$info['uid']]);

        if ($res){
            return Util::successArrayReturn();
        }else{
            return Util::errorArrayReturn();
        }
    }
}