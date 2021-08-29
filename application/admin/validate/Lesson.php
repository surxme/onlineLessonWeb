<?php

namespace app\admin\validate;

use think\Validate;

class Lesson extends Validate
{
    protected $rule=[
        'name'=>'require|max:40',
        'type_id'=>'require',
        'poster'=>'require',
        'teacher_ids'=>'require',
    ];
    protected $message=[
        'name.require'=>'未填写课程名',
        'name.max'=>'课程名长度超过40',
        'type_id'=>'请选择课程类别',
        'poster.require'=>'请上传封面',
        'teacher_ids.require'=>'请选择关联教师'
    ];
}
