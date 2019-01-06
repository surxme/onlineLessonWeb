<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/5
 * Time: 11:43
 */

namespace app\admin\controller;


use app\admin\model\Util;
use think\Request;

class FileUpload extends  BaseController
{
    /**
     * 普通文件上传
     * @return string
     */
    public function index(){
        $file = request()->file('file');
        if($file){
            $info = $file->rule('uniqid')->move(ROOT_PATH  . 'public' . DS  . 'static' .DS . 'uploads' . DS . 'poster');
            if ($info) {//上传成功
                $file_name = $info->getFilename();
                $file_url = DS . 'uploads' . DS . 'poster' . DS .$file_name;

//                $file_url = DSFormat($file_url);

                return Util::successJsonReturn(['msg' => '上传成功','file_url'=>$file_url,'file_name'=>$file_name]);
            }else{//上传失败
                return Util::errorJsonReturn(['msg'=>'上传失败']);
            }
        }else{
            return Util::errorJsonReturn(['msg' => '文件不能为空']);
        }
    }

    /**
     * 富文本编辑器的文件上传 （需要回传layui前端框架特定格式的json）
     * @return string
     */
    public function layeditPicUpload(){
        $base = request()->root();
        //bigdata/public
        $root    = strpos($base, '.') ? ltrim(dirname($base), DS) : $base;

        $file = request()->file('file');
        if($file){
            $info = $file->rule('uniqid')->move(ROOT_PATH  . 'public' . DS  . 'static' .DS . 'uploads' . DS . 'layedit');
            if ($info) {//上传成功
                $file_name = $info->getFilename();
//                $file_url = ROOT_PATH  . 'public' . DS . 'uploads' . DS . 'layedit' . DS .$file_name;
                $file_url = $root . DS . 'uploads' . DS . 'layedit' . DS .$file_name;
//                $file_url = DSFormat($file_url);

                return Util::layeditJsonReturn(['data'=>['src'=>$file_url,'title'=>$file_name]]);
            }else{//上传失败
                return Util::layeditJsonReturn(['code' => 1,'msg' => '操作失败','data'=>[]]);
            }
        }else{
            return Util::layeditJsonReturn(['code' => 1,'msg' => '操作失败','data'=>[]]);
        }
    }
}