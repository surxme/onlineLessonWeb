<?php

namespace app\admin\validate;

use think\Validate;

class Teacher extends Validate
{
    protected $rule=[
        'name'=>'require|max:20',
        'dept_id'=>'require',
        'bir'=>'require',
        'email'=>'email|require',
        'avatar'=>'require',
    ];
    protected $message=[
        'name.require'=>'未填写教师名',
        'name.max'=>'教师名长度超过20',
        'dept_id'=>'请选择部门',
        'bir.require'=>'请填写生日',
        'email.email'=>'邮箱格式不正确',
        'email.require'=>'请填写邮箱',
        'avatar.require'=>'请上传头像',

    ];
}
