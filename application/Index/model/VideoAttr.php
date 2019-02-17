<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/2/16
 * Time: 20:55
 */

namespace app\index\model;


use think\Db;
use think\Model;

class VideoAttr extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp=true;
    protected $createTime='create_time';
    protected $updateTime=false;

    public function search($params){
        $where = [];
        $list = Db::name('video_attr')->alias('t')->join('t_video video','t.video = video.id')
            ->join('t_lesson lesson','video.lesson_id = lesson.id');

        if(isset($params['search_key'])){
            $where['video.name|lesson.name'] = array('like','%'.$params['search_key'].'%');
        }

        $field = 't.*,lesson.name as lesson_name,lesson.poster as poster';

        $list = $list->where($where)->field($field)->order('id desc')->paginate(10);

        return $list;
    }
}