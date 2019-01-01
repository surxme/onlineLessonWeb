<?php

namespace app\admin\controller;

use think\Controller;

class Error extends Controller
{
    public function index()
    {
        $this->redirect('admin/index');
    }

    public function _empty(){
        $this->redirect('admin/index');
    }
}
