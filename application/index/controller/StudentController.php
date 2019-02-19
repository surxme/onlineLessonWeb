<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/2/19
 * Time: 21:41
 */

namespace app\index\controller;


class StudentController extends BaseController
{
    public function index(){
        return $this->fetch('student/index');
    }
}