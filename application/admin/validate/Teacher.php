<?php

namespace app\admin\validate;

use think\Validate;

class Teacher extends Validate
{
    protected $rule=[
        'name'=>'require|max:20|chsDash',
        'teacher_no'=>'require|unique:teacher',
        'dept_id'=>'require',
        'bir'=>'require',
        'email'=>'email|require|unique:teacher',
        'avatar'=>'require',
    ];
    protected $message=[
        'name.require'=>'未填写教师名',
        'name.max'=>'教师名长度超过20',
        'name.chsDash'=>'教师名只能是汉字、字母、数字和下划线_及破折号-',
        'teacher_no.unique'=>'教师职工号已存在',
        'dept_id'=>'请选择部门',
        'bir.require'=>'请填写生日',
        'email.email'=>'邮箱格式不正确',
        'email.require'=>'请填写邮箱',
        'email.unique'=>'邮箱已被使用',
        'avatar.require'=>'请上传头像',
    ];
}
