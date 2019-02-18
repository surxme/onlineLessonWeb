<?php

namespace app\index\validate;

use think\Validate;

class Comment extends Validate
{
    protected $rule=[
        'content'=>'require',
    ];
    protected $message=[
        'content.require'=>'请输入正确的提交信息',
    ];
}
