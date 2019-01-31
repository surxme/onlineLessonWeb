<?php

namespace app\admin\validate;

use think\Validate;

class Type extends Validate
{
    protected $rule=[
        'name'=>'require|max:20|unique:type|chsDash',
    ];
    protected $message=[
        'name.require'=>'未填写类别名',
        'name.max'=>'类别名长度超过20',
        'name.unique'=>'类别名已存在',
        'name.chsDash'=>'类别名只能是汉字、字母、数字和下划线_及破折号-',
    ];
}
