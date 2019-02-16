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

        $subsql = Db::name('video')->group('lesson_id')->field('lesson_id,count(*) as counts')->select(false);

        if(isset($params['search_key'])){
            $where['t.name'] = array('like','%'.$params['search_key'].'%');
        }

        //子查询联查视频不为空的课程做展示
        $list = $list->join(['('.$subsql.')'=>'v'],'v.lesson_id = t.id')->where($where);

        $list = $list->paginate($pageSize)->each(function ($item,$key){
            $teachers = Teacher::where('id','in',$item['teacher_ids'])->column('name');
            $item['teachers_name'] = implode(',',$teachers);
            return $item;
        });

        return $list;
    }

    public static function getOneLessonById($id,$video_id=0){
        $subsql = Db::name('video')->group('lesson_id')->field('lesson_id,count(*) as counts')->select(false);

        //课程基本信息
        $lesson = Db::name('lesson')->alias('t')
            ->join('t_type type','t.type_id = type.id')
            ->where('t.id',$id)
            ->field('t.*,type.name as typename')
            ->find();

        //课程视频列表
        $video_list = Db::name('video')->where('lesson_id',$id)->field('name,id,lesson_id')->select();

        //如果有video_id查询的视频为video_id对应的视频
        if($video_id){
            $video = Db::name('video')->alias('t')->join('teacher','t.teacher_id = teacher.id','left')
                ->where('t.id',$video_id)
                ->field('t.*,teacher.name as teacher_name,teacher.avatar')
                ->find();
        }else{
            $video = Db::name('video')->alias('t')->join('teacher','t.teacher_id = teacher.id','left')
                ->where('lesson_id',$id)
                ->field('t.*,teacher.name as teacher_name,teacher.avatar')
                ->find();
        }

        //相关推荐
        $suggestion = Db::name('lesson')->alias('t')->where('type_id',$lesson['type_id'])
            ->whereNotIn('t.id',$id)
            ->join(['('.$subsql.')'=>'v'],'v.lesson_id = t.id')
            ->field('id,name,poster,hits')
            ->limit(3)
            ->order('hits','desc')
            ->select();

        return [
            'lesson'=>$lesson,
            'video_list'=>$video_list,
            'video'=>$video,
            'suggestion'=>$suggestion
        ];
    }

    public static function getVideosListByLessonID($lesson_id){
        $list = Db::table('video')->where('lesson_id',$lesson_id)->select();
        return $list;
    }
}