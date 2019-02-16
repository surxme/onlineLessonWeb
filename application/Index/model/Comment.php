<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/27
 * Time: 15:34
 */

namespace app\index\model;


use app\admin\model\UserBehavior;
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

    public static function search($type,$video_id){
        $where = ['type' => $type,'user_type'=>UserBehavior::USER_TYPE_STUDENT,'comment_id'=>0,'floor_id'=>0,'data_id'=>$video_id];

        $list = Db::name('comment')->alias('t')
            ->join('video','t.data_id = video.id')
            ->join('student','t.uid = student.id');

        $field = 't.*,student.name as student_name,student.avatar,student.id as student_id';

        $list =$list->where($where)->order('id desc')->field($field)->paginate(10);

        return $list;
    }
}