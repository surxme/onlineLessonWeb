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
            $where['t.name|t.student_no'] = array('like','%'.$params['search_key'].'%');
        }
        $list = $list->where($where);
        $list = $list->paginate($pageSize);

        return $list;
    }
}