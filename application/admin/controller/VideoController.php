<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/1
 * Time: 20:54
 */

namespace app\admin\controller;

use app\admin\model\Type;
use app\admin\model\Util;
use app\admin\model\Video;
use think\Db;

class VideoController extends BaseController
{
    /**
     * @return mixed
     */
    public function  index(){
        $params = input('param.');
        $list = (new Video())->search($params);
        $this->assign('list',$list);
        return $this->fetch('video/index');
    }

    /**
     * 删除
     */
    public function del(){
        $id=input('param.id');
        $re= (new Video())->where('id',$id)->delete();
        if ($re){
            return Util::successArrayReturn();
        }else{
            return Util::errorArrayReturn();
        }
    }
}