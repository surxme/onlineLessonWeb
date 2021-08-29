<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/27
 * Time: 15:34
 */

namespace app\index\model;

use think\Db;
use think\Model;

class Notice extends Model
{
    const TYPE_REPLY = 1;
    const TYPE_NEW_LESSON = 2;
    protected $pk = 'id';
    protected $autoWriteTimestamp=true;
    protected $createTime='create_time';
    protected $updateTime=false;

    /**
     * @param $type 消息类型
     * @param $receive_uid 接收人id
     * @param $user_type 接收人类型 1学生2教师
     * @return $this|\think\Paginator
     * @throws \think\exception\DbException
     */
    public static function search($type,$receive_uid,$user_type){
        $where = ['type' => $type,'user_type'=>$user_type,'receive_id'=>$receive_uid];
        $field = 't.*,student.name as student_name,student.avatar,student.id as student_id';
        $list =Db::name('notice')->alias('t')->where($where)->order('id desc')->field($field)->paginate(10);

        return $list;
    }
}