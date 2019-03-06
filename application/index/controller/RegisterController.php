<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/3/6
 * Time: 23:48
 */

namespace app\index\controller;


use think\Controller;

class RegisterController extends Controller
{
    public function index(){
        return $this->fetch('login/register');
    }
}