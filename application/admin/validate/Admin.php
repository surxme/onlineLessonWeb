<?php

namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{
    protected $rule=[
        'name'=>'require|max:10|chsDash',
    ];
    protected $message=[
        'name.require'=>'未填写用户名',
        'name.max'=>'用户名长度超过10',
        'name.chsDash'=>'用户名只能是汉字、字母、数字和下划线_及破折号-',
    ];
}
