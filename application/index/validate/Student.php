<?php

namespace app\index\validate;

use think\Validate;

class Student extends Validate
{
    protected $rule=[
        'name'=>'require|max:20|chsDash',
        'student_no'=>'require|unique:student',
        'bir'=>'require',
        'email'=>'email|require|unique:student',
        'avatar'=>'require',
//        'password'=>'min:6|max:12',
    ];
    protected $message=[
        'name.require'=>'未填写学生名',
        'name.max'=>'学生名长度超过20',
        'name.chsDash'=>'学生名只能是汉字、字母、数字和下划线_及破折号-',
        'student_no.unique'=>'学号已存在',
        'bir.require'=>'请填写生日',
        'email.email'=>'邮箱格式不正确',
        'email.require'=>'请填写邮箱',
        'email.unique'=>'邮箱已被使用',
        'avatar.require'=>'请上传头像',
//        'password.min'=>'密码为6-12位',
//        'password.max'=>'密码为6-12位',
    ];
}
