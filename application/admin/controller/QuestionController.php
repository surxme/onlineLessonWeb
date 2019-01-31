<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/27
 * Time: 20:54
 */

namespace app\admin\controller;

use app\admin\model\Comment;

class QuestionController extends BaseController
{
    /**
     * @return mixed
     */
    public function  index(){
        $params = input('param.');
        $list = (new Comment())->search(Comment::TYPE_QUESTION,$params);
        $this->assign('list',$list);
        return $this->fetch('question/index');
    }
}