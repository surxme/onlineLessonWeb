<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/27
 * Time: 21:23
 */

namespace app\admin\model;


use think\Db;
use think\Model;

class Student extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp=true;
    protected $createTime='create_time';
    protected $updateTime='update_time';

    public function search($params,$pageSize = 10){
        $where = [];
        $list = Db::name('student')->alias('t')->order('id desc');

        if(isset($params['search_key'])){
            $where['t.name'] = array('like','%'.$params['search_key'].'%');
        }
        $list = $list->where($where);
        $list = $list->paginate($pageSize);

        return $list;
    }
}