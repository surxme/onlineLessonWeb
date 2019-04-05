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

    public function search($params = [],$pageSize = 12){
        $where = [];
        $list = Db::name('lesson')->alias('t');

        $subsql = Db::name('video')->group('lesson_id')->field('lesson_id,count(*) as counts,sum(hits) as l_hits')->select(false);

        if(isset($params['search_key'])){
            $where['t.name'] = array('like','%'.$params['search_key'].'%');
        }

        if(isset($params['ltype'])&&$params['ltype']!=''){
            $where['t.type_id'] = $params['ltype'];
        }

        if(isset($params['teacher_id'])){
            $list->join('lesson_attr la','la.lesson_id = t.id');
            $where['la.teacher_id'] = array('in',$params['teacher_id']);
        }

        //子查询联查视频不为空的课程做展示
        $list = $list->join(['('.$subsql.')'=>'v'],'v.lesson_id = t.id')->where($where);

        if(isset($params['order'])){
            if($params['order'] == 1){
                $list->order('t.create_time desc');
            }
            if($params['order'] == 2){
                $list->order('v.l_hits desc');
            }
        }

        $list = $list->paginate($pageSize)->each(function ($item,$key){
            $teachers = Teacher::where('id','in',$item['teacher_ids'])->column('name');
            $item['teachers_name'] = implode(',',$teachers);
            return $item;
        });

        return $list;
    }

    public static function getOneLessonById($id,$video_id=0){
        $subsql = Db::name('video')->group('lesson_id')->field('lesson_id,count(*) as counts,sum(hits) as hits,sum(thumbs) as thumbs')->select(false);

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

        $video['path_url'] = json_decode($video['path'],true)[0]['url'];

        //相关推荐
        $suggestion = Db::name('lesson')->alias('t')->where('type_id',$lesson['type_id'])
            ->whereNotIn('t.id',$id)
            ->join(['('.$subsql.')'=>'v'],'v.lesson_id = t.id')
            ->field('id,name,poster,v.hits')
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