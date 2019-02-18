<?php
namespace app\index\controller;

use think\Controller;

class BaseController extends Controller
{
    public function _empty(){
        $this->redirect('index/index');
    }
}