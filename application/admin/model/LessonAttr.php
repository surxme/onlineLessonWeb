<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/27
 * Time: 16:28
 */

namespace app\admin\model;


use think\Db;
use think\Model;

class LessonAttr extends Model
{
    protected $pk = 'id';
    protected $name = 'lesson_attr';
    protected $autoWriteTimestamp=true;
    protected $createTime='create_time';

    public function search($params){
        $where = [];
        $list = Db::name('video')->alias('t')->join('t_teacher teacher','t.teacher_id = teacher_id','LEFT')
            ->join('t_lesson lesson','t.lesson_id = lesson.id','LEFT');

        if(isset($params['search_key'])){
            $where['name'] = array('like','%'.$params['search_key'].'%');
        }

        $field = 't.*,lesson.name as lesson_name,teacher.name as teacher_name';

        $list = $list->where($where)->field($field)->order('id desc')->paginate(10);

        return $list;
    }
}