<?php

namespace app\index\validate;

use think\Validate;

class Subscribe extends Validate
{
    protected $rule=[
        'teacher_id'=>'require|>:0',
        'uid'=>'require',
        'u_type'=>'require',
    ];
    protected $message=[
        'teacher_id.require'=>'参数错误',
        'uid.require'=>'无法获取已登录用户,请重新登录再试',
        'u_type.require'=>'无法获取已登录用户,请重新登录再试',
    ];
}
