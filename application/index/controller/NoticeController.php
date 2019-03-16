<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/3/16
 * Time: 12:54
 */

namespace app\index\controller;

use app\admin\model\Util;
use app\index\model\Notice;
use app\index\model\UserBehavior;
use think\Db;

class NoticeController extends BaseController
{
    /**
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function index(){
        $type = input('param.type');
        if(!in_array($type,[1,2])){
            $this->error('参数错误');
        }

        $where = [
            'receive_id'=>$this->_cur_user['id'],
            'receive_user_type'=>$this->_cur_user['u_type'],
            'type' => $type
        ];
        //如果type=1表示查看回复，需要显示回答的内容和回复人的名字
        //如果是新课程通知则需要显示新课程的名字和通知人的名字
        $list = Db::name('notice')->where($where)
            ->paginate(2)
            ->each(function ($item,$key){
                if($item['user_type'] == UserBehavior::USER_TYPE_STUDENT){
                    $user = Db::name('student')->where('id',$item['uid'])->find();
                    $item['user_name'] = $user['name'];
                }else{
                    $user = Db::name('teacher')->where('id',$item['uid'])->find();
                    $item['user_name'] = $user['name'];
                }
                if($item['type'] == 1){
                    $content = Db::name('comment_reply')->where('id',$item['detail_id'])->find();
                    $item['content'] = $content['content'];
                    $item['question_id'] = $content['data_id'];
                }else{
                    $content = Db::name('video')->where('id',$item['detail_id'])->find();
                    $item['content'] = $content['name'];
                    $item['lesson_id'] = $content['lesson_id'];
                }

                return $item;
            });
        $this->assign('list',$list);
        $this->assign('type',$type);
        return $this->fetch('notice/index');
    }

    public function getNewNoticeNum(){
        $user = $this->_cur_user;
        if (empty($user)){
            return Util::errorArrayReturn(['msg'=>'暂未登录']);
        }
        $where = [
            'receive_user_type' => $user['u_type'],
            'receive_id' => $user['id'],
            'status' => 0
        ];
        $notice = new Notice();
        $new_lesson = $notice->where($where)->where( 'type' , Notice::TYPE_NEW_LESSON)->count();
        $new_reply = $notice->where($where)->where( 'type' , Notice::TYPE_REPLY)->count();
        $all = $new_lesson+$new_reply;

        $count_arr = [
            'new_lesson'=>$new_lesson?$new_lesson:'',
            'new_reply'=>$new_reply?$new_reply:'',
            'all'=> $all?$all:''
        ];

        return Util::successArrayReturn(['count_arr'=>$count_arr]);
    }
}