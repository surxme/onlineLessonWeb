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

class VerifyCode extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp=true;
    protected $createTime = 'create_time';
    protected $updateTime = false;

    public static function findByUid($email,$u_type = UserBehavior::USER_TYPE_STUDENT){
        $where = ['email'=>$email,'u_type'=>$u_type];
        $code = Db::name('verify_code')->alias('t')->order('id desc');

        $code = $code->where($where);

        return $code;
    }
}