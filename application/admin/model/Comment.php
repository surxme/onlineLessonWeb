<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/27
 * Time: 15:34
 */

namespace app\admin\model;


use think\Db;
use think\Model;

class Comment extends Model
{
    const TYPE_COMMENT = 1;
    const TYPE_QUESTION = 2;
    protected $pk = 'id';
    protected $autoWriteTimestamp=true;
    protected $createTime='create_time';
    protected $updateTime='update_time';

    public function search($type,$params){
        $where = ['type' => $type];
        $list = Db::name('comment')->alias('t')
            ->join('video','t.data_id = video.id','LEFT')
            ->join('lesson','video.lesson_id = lesson.id','LEFT');

        if(isset($params['search_key'])){
            $where['name'] = array('like','%'.$params['search_key'].'%');
        }
        $field = 't.*,video.name as video_name,lesson.name as lesson_name,lesson.id as lesson_id';

        $list =$list->where($where)->order('id desc')->field($field)->paginate(10)->each(function($item, $key){
            $item['type_name'] = $item['user_type']==1?'学生':'教师';
            $user = Student::get($item['uid']);
            if($item['user_type']==1){
                $item['name'] = $user['name'];
                $item['is_banned'] = $user['is_banned'];
            }else{
                $item['name'] = Teacher::get($item['uid'])['name'];
            }
            return $item;
        });

        return $list;
    }
}