<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/1
 * Time: 20:07
 */

namespace app\index\model;


use think\Db;
use think\Model;

class UserBehavior extends Model
{
    Const ACTION_TYPE_LOGIN = 1;
    Const ACTION_TYPE_WATCH_VIDEO = 2;
    Const ACTION_TYPE_WATCH_COMMENT = 3;
    Const ACTION_TYPE_WATCH_QUESTION = 4;

    Const USER_TYPE_STUDENT = 1;
    Const USER_TYPE_TEACHER = 2;
    Const USER_TYPE_ADMIN = 3;

    protected $pk = 'id';
    protected $autoWriteTimestamp=true;
    protected $createTime='create_time';
    protected $updateTime=false;

    /**
     * $data array(user_type=>'',uid=>'',action_type=>'')
     * @param $data
     * @param $data_id
     * @return int|string
     */
    public static function insertBehavior($data,$data_id=0){
        if($data_id){
            $data['data_id'] = $data_id;
            $data['create_time'] = time();
        }
        return Db::name('user_behavior')->insert($data);
    }

    /**
     * * 获取上传某个操作的时间
     * @param $where array(user_type=>'',uid=>'',action_type=>'')
     * @param int $data_id
     * @return array|false|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getLastActionTime($where,$data_id=0){
        if($data_id){
            $where['data_id'] = $data_id;
        }
        $time = Db::name('user_behavior')->where($where)->order('id desc')->field('create_time')->find();
        return $time;
    }

    public function getLastLoginRecord($uid,$user_type,$action_type){
        $data  = $this->where('user_type',$user_type)
                     ->where('action_type',$action_type)
                     ->where( 'uid' ,$uid)
                     ->order('id desc')->limit(3)->column('create_time');
        return $data;
    }
}