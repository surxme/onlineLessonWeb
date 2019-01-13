<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/5
 * Time: 20:53
 */

namespace app\admin\model;


use think\Db;
use think\Model;

class Teacher extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp=true;
    protected $createTime='create_time';
    protected $updateTime='update_time';

    public function search($params,$pageSize = 10){
        $where = [];
        $list = Db::name('teacher')->alias('t')->order('id desc')->join('t_dept dept','t.dept_id = dept.id','LEFT');

        if(isset($params['dept_ids'])){
            $where['dept_id'] = array('in',implode(',',$params['dept_ids']));
        }

        if(isset($params['search_key'])){
            $where['t.name'] = array('like','%'.$params['search_key'].'%');
        }
        $list = $list->where($where);
        $list = $list->field('t.*,dept.name as dept_name')->paginate($pageSize);

        return $list;
    }
}