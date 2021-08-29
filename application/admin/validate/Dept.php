<?php

namespace app\admin\validate;

use think\Validate;

class Dept extends Validate
{
    protected $rule=[
        'name'=>'require|max:20|unique:dept|chsDash',
    ];
    protected $message=[
        'name.require'=>'部门名不能为空',
        'name.max'=>'部门名长度部门超过20',
        'name.unique'=>'部门已存在',
        'name.chsDash'=>'部门名只能是汉字、字母、数字和下划线_及破折号-',
    ];
}
