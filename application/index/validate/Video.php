<?php

namespace app\index\validate;

use think\Validate;

class Video extends Validate
{
    protected $rule=[
        'name'=>'require',
        'lesson_id'=>'require',
        'teacher_id'=>'require',
        'path'=>'require',
    ];
    protected $message=[
        'name.require'=>'未填写学生名',
        'name.chsDash'=>'视频名只能是汉字、字母、数字和下划线_及破折号-',
        'lesson_id.require'=>'所属课程不能为空',
        'teacher_id.require'=>'无法获取提交人',
        'path.require'=>'视频上传失败',
    ];
}
