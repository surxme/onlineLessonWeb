<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/2/10
 * Time: 21:55
 */

namespace app\index\model;


use think\Db;
use think\Model;

class Video extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp=true;
    protected $createTime='create_time';
    protected $updateTime='update_time';

    public function search($params){
        $where = [];
        $list = Db::name('video')->alias('t')->join('t_teacher teacher','t.teacher_id = teacher.id','LEFT')
            ->join('t_lesson lesson','t.lesson_id = lesson.id','LEFT');

        if(isset($params['search_key'])){
            $where['t.name|teacher.name|lesson.name'] = array('like','%'.$params['search_key'].'%');
        }

        $field = 't.*,lesson.name as lesson_name,lesson.poster as poster,teacher.name as teacher_name';

        $list = $list->where($where)->field($field)->order('id desc')->paginate(10);

        return $list;
    }
}