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
use traits\model\SoftDelete;

class Student extends Model
{
    use SoftDelete;
    protected $pk = 'id';
    protected static $deleteTime = 'delete_time';
    protected $autoWriteTimestamp=true;
    protected $createTime='create_time';
    protected $updateTime='update_time';

    public function search($params,$pageSize = 10){
        $where = ['is_del'=>0];
        $list = Db::name('student')->alias('t')->order('id desc');

        if(isset($params['search_key'])){
            $where['t.name|t.student_no'] = array('like','%'.$params['search_key'].'%');
        }
        $list = $list->where($where);
        $list = $list->paginate($pageSize);

        return $list;
    }
}