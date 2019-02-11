<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/2/1
 * Time: 20:53
 */

namespace app\index\model;


use think\Db;
use think\Model;

class Lesson extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp=true;
    protected $createTime='create_time';
    protected $updateTime='update_time';

    public function search($params,$pageSize = 12){
        $where = [];
        $list = Db::name('lesson')->alias('t')->order('id desc');

        if(isset($params['search_key'])){
            $where['t.name'] = array('like','%'.$params['search_key'].'%');
        }

        $list = $list->where($where);
        $list = $list->paginate($pageSize)->each(function ($item,$key){
            $teachers = Teacher::where('id','in',$item['teacher_ids'])->column('name');
            $item['teachers_name'] = implode(',',$teachers);
            return $item;
        });

        return $list;
    }

    public static function getOneLessonById($id,$video_id=0){
        if($video_id==0){
            $data = Db::name('lesson')->alias('t')->where('id',$id)->find();
        }
        return $data;
    }

    public static function getVideosListByLessonID($lesson_id){
        $list = Db::table('video')->where('lesson_id',$lesson_id)->select();
        return $list;
    }
}