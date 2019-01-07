<?php

namespace app\admin\validate;

use think\Validate;

class Teacher extends Validate
{
    protected $rule=[
        'name'=>'require|max:20',
        'dept_id'=>'require',
        'email'=>'email|require',
    ];
    protected $message=[
        'name.require'=>'未填写课程名',
        'name.max'=>'课程名长度超过20',
        'dept_id'=>'请选择部门',
        'email.email'=>'邮箱格式不正确',
        'email.require'=>'请填写邮箱',
    ];
}
