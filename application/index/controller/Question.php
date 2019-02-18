<?php
namespace app\index\controller;

use app\index\model\Lesson;

class Question extends BaseController
{
    /**
     * 问答详情
     * @return mixed
     */
    public function index(){
        $params = input('param.id');
        $list = (new Lesson())->search($params);
        $this->assign('list', $list);
        return $this->fetch('index');
    }
}
