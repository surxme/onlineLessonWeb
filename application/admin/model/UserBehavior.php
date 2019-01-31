<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/1
 * Time: 20:07
 */

namespace app\admin\model;


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

    /**
     * @param $data array(user_type=>'',uid=>'',action_type=>'')
     */
    public function insertBehavior($data){
        $data['create_time'] = time();
        $this->save($data);
    }

    public function getLastLoginRecord($uid,$user_type){
        $data  = $this->where('user_type',$user_type)
                     ->where('action_type',UserBehavior::ACTION_TYPE_LOGIN)
                     ->where( 'uid' ,$uid)
                     ->limit(1,1)->order('id', 'desc')->value('create_time');
        return $data;
    }
}