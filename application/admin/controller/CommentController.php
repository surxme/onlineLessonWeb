<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/1
 * Time: 20:54
 */

namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\model\Comment;
use app\admin\model\Student;
use app\admin\model\Type;
use app\admin\model\Util;
use think\Db;

class CommentController extends BaseController
{
    /**
     * @return mixed
     */
    public function  index(){
        $params = input('param.');
        $list = (new Comment())->search(Comment::TYPE_COMMENT,$params);
        $this->assign('list',$list);
        return $this->fetch('comment/index');
    }

    /**
     * 删除
     * @return array
     */
    public function del(){
        $id=input('param.id');
        $re= (new Comment())->where('id',$id)->delete();
        if ($re){
            return Util::successArrayReturn();
        }else{
            return Util::errorArrayReturn();
        }
    }

    /**
     * 禁言
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function ban(){
        $id = input('param.id');
        $info = Comment::get($id);

        $res = Student::update(['is_banned'=>1],['id'=>$info['uid']]);

        if ($res){
            return Util::successArrayReturn();
        }else{
            return Util::errorArrayReturn();
        }
    }

    /**
     * 解除禁言
     * @return array
     * @throws \think\exception\DbException
     */
    public function relieveBan(){
        $id = input('param.id');
        $info = Comment::get($id);

        $res = Student::update(['is_banned'=>0],['id'=>$info['uid']]);

        if ($res){
            return Util::successArrayReturn();
        }else{
            return Util::errorArrayReturn();
        }
    }
}