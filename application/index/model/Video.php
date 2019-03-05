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

    /**
     * '原始文件名_|_上传后生成的文件名_|_上传后生成的地址_|_上传后的大小'
     * @param $attachments
     * @return array
     */
    public static function explodeAttachments($attachments){
        $attach_arr = explode(',',$attachments);

        $attach_data = [];
        $attach_key = ['name','file_name','url','size'];

        foreach ($attach_arr as $attach){
            $attach_val = explode('_|_',$attach);
            $attach_data[] = array_combine($attach_key,$attach_val);
        }

        return $attach_data;
    }

    /**
     * @param $attachments_str
     * @return string
     */
    public static function implodeAttachments($attachments_str){
        $attach_arr = json_decode($attachments_str,true);
        $attach_str = [];

        foreach ($attach_arr as $attach){
            $attach_str[] = implode('_|_',$attach);
        }

        return implode(',',$attach_str);
    }

    public static function initAttachmentData($attachments_str){
        $attach_arr = json_decode($attachments_str,true);
        $attach_str = [];

        foreach ($attach_arr as $attach){
            $attach_str[$attach['name']] = implode('_|_',$attach);
        }

        return json_encode($attach_str);
    }

    public static function implodePath($json_str){
        $path_arr = json_decode($json_str,true);
        $path_str[] = implode('_|_',$path_arr);
        return implode(',',$path_str);
    }

    public static function initPathData($path_str){
        $path_arr = json_decode($path_str,true);
        $path_str[$path_arr['name']] = implode('_|_',$path_arr);
        return json_encode($path_str);
    }
}