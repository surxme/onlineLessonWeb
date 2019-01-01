<?php
namespace app\admin\controller;

use think\Db;

class Index extends BaseController
{
    public function index()
    {
        return $this->fetch('index/index');
    }
}
