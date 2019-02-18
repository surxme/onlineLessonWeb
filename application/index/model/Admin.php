<?php
namespace app\index\model;

use think\Model;
use think\Session;

class Admin extends Model
{
    /**
     * 获取当前用户id和用户类型
     * @return array
     */
    public static function getCurUserID(){
        $type = Session::get('bigdata_user_type');
        $id = 0;
        if(Session::has('bigdata_user_type')){
            if($type == UserBehavior::USER_TYPE_STUDENT){
                $id = Session::get('bigdata_student_id');
            }else{
                $id = Session::get('bigdata_teacher_id');
            }
        }
        return [$id,$type];
    }

    /**获取当前学生id
     * @return string
     */
    public static function getCurStudentID(){
        $id = Session::get('bigdata_student_id');
        return $id;
    }

    /**获取当前教师id
     * @return string
     */
    public static function getCurTeacherID(){
        $id = Session::get('bigdata_teacher_id');
        return $id;
    }
}