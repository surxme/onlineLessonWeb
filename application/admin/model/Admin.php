<?php
namespace app\admin\model;

use think\Db;
use think\Model;
use think\Session;

class Admin extends Model
{
    const USER_TYPE_STUDENT = 1;
    const USER_TYPE_TEACHER = 2;
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public function search($params){
        $where = [];
        $list = Db::name('admin')->alias('t');
        $where['id'] = ['<>',self::getCurAdminID()];

        if(isset($params['search_key'])){
            $where['name'] = array('like','%'.$params['search_key'].'%');
        }
        $field = 't.*';

        $list =$list->where($where)->order('id desc')->field($field)->paginate(10);

        return $list;
    }

    public static function getCurAdminID(){
        return Session::get('bigdata_admin_id');
    }

    public static function passwordfix($str){
        return md5(md5($str).'bigdata');
    }
}