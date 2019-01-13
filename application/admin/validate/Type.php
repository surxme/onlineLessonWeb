<?php

namespace app\admin\validate;

use think\Validate;

class Type extends Validate
{
    protected $rule=[
        'name'=>'require|max:20',
    ];
    protected $message=[
        'name.require'=>'未填写类别名',
        'name.max'=>'类别名长度超过20',
    ];
}
