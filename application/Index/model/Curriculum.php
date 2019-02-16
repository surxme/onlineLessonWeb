<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/2/16
 * Time: 22:55
 */

namespace app\index\model;


use think\Db;
use think\Model;

class Curriculum extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp=true;
    protected $createTime='create_time';

    public function search($params){
        $where = [];
        $list = Db::name('curriculum')->alias('t')
            ->join('t_lesson lesson','t.lesson_id = lesson.id');

        if(isset($params['search_key'])){
            $where['lesson.name'] = array('like','%'.$params['search_key'].'%');
        }

        $field = 't.*,lesson.name as lesson_name,lesson.poster as poster';

        $list = $list->where($where)->field($field)->order('id desc')->paginate(10);

        return $list;
    }
}