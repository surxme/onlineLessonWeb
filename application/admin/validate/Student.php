<?php

namespace app\admin\validate;

use think\Validate;

class Student extends Validate
{
    protected $rule=[
        'name'=>'require|max:20',
        'bir'=>'require',
        'email'=>'email|require',
        'avatar'=>'require',
    ];
    protected $message=[
        'name.require'=>'未填写学生名',
//        'name.unique'=>'学生名已存在',
        'name.max'=>'学生名长度超过20',
        'bir.require'=>'请填写生日',
        'email.email'=>'邮箱格式不正确',
        'email.require'=>'请填写邮箱',
        'email.unique'=>'邮箱已被使用',
        'avatar.require'=>'请上传头像',

    ];
}
