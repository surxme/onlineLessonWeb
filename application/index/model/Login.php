<?php
namespace app\index\model;

use think\Db;
use think\Model;
use think\Session;

class Login extends Model
{
    /**
     * @param $name
     * @param $pass
     * @param $type
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
	public function login($name,$pass,$type){
	    $table =$this->getTableName($type);
        $user=Db::name($table)->where("email|".$table.'_no',$name)->where('is_del',0)->find();
        if(!empty($user)){
            if($this->getMd5Password($pass)!=$user['password']){
                return 1;
            }else{
                Session::set('bigdata_user_name',$user['name']);
                Session::set('bigdata_'.$table.'_id',$user['id']);
                Session::set('bigdata_user_type',$type);
                return 3;
            }
        }else{
            return 2;
        }
	}

	public function getMd5Password($str){
        return md5(md5($str).'bigdata');
    }

    private function getTableName($type){
	    if($type==UserBehavior::USER_TYPE_STUDENT){
	        return 'student';
        }else{
	        return 'teacher';
        }
    }
}