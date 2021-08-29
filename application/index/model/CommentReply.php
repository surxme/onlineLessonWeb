<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/2/1
 * Time: 21:23
 */

namespace app\index\model;


use think\Db;
use think\Model;

class CommentReply extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp=true;
    protected $createTime = 'create_time';
    protected $updateTime = false;
}