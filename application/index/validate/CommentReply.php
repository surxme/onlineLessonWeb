<?php

namespace app\index\validate;

use think\Validate;

class CommentReply extends Validate
{
    protected $rule=[
        'content'=>'require',
        'data_id'=>'require',
        'uid'=>'require',
    ];
    protected $message=[
        'content.require'=>'内容不能为空',
        'data_id.require'=>'评论的内容不存在或已被删除',
        'uid.require'=>'请检测是否登录或刷新重试',
    ];
}
