<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/2/16
 * Time: 22:55
 */

namespace app\index\model;


use think\Db;
use think\Model;

class Subscribe extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp=true;
    protected $createTime='create_time';
    protected $updateTime=false;

}