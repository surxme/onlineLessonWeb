<?php
namespace app\admin\controller;

use think\Controller;
use think\Session;

class BaseController extends Controller
{
    public function initialize(){
        if(!Session::has('bigdata_admin_id')){
            $this->redirect('admin/login');
        }
    }

    public function _empty(){
        $this->redirect('admin/index');
    }
}