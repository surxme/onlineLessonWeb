<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/5
 * Time: 11:43
 */

namespace app\admin\controller;


use app\admin\model\Admin;
use app\admin\model\Teacher;
use app\admin\model\Util;
use PHPExcel_IOFactory;
use think\Db;

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
        $root = strpos($base, '.') ? ltrim(dirname($base), DS) : $base;

        $file = request()->file('file');
        if($file){
            $info = $file->rule('uniqid')->move(ROOT_PATH  . 'public' . DS  . 'static' .DS . 'uploads' . DS . 'layedit');
            if ($info) {//上传成功
                $file_name = $info->getFilename();
//                $file_url = ROOT_PATH  . 'public' . DS . 'uploads' . DS . 'layedit' . DS .$file_name;
                $file_url = $root . DS . 'static' .DS  . 'uploads' . DS . 'layedit' . DS .$file_name;
//                $file_url = DSFormat($file_url);

                return Util::layeditJsonReturn(['data'=>['src'=>$file_url,'title'=>$file_name]]);
            }else{//上传失败
                return Util::layeditJsonReturn(['code' => 1,'msg' => '操作失败','data'=>[]]);
            }
        }else{
            return Util::layeditJsonReturn(['code' => 1,'msg' => '操作失败','data'=>[]]);
        }
    }

    public function PhpExcelImport(){
        $excel = request()->file('file')->getInfo();//excel为file中的name
        import('phpexcel.PHPExcel', EXTEND_PATH);
        $objPHPExcel = PHPExcel_IOFactory::load($excel['tmp_name']);//读取上传的文件
        $arrExcel = $objPHPExcel->getSheet(0)->toArray();//获取其中的数据

        $default_dept_id = Db::name('dept')
            ->where('p_id=0')
            ->field('id,name')
            ->limit(1)
            ->find()['id'];
        $success_count = 0;
        $fail_counts = 0;

        $fail_array = array();

        $teacher = new Teacher();

        foreach ($arrExcel as $key => $value) {
            $data = array(
                'name'=>$arrExcel[$key][0],
                'password'=>Admin::passwordfix('t123456'),
                'sex' => $arrExcel[$key][1]=='女'?2:1,
                'bir' => strtotime($arrExcel[$key][3]),
                'avatar' => DS . 'uploads' . DS . 'poster'.DS.'logo.svg',
                'email' => $arrExcel[$key][4],
                'dept_id' =>$default_dept_id,
                'create_time' => time(),
                'update_time' => time()
            );

            $id = $teacher->validate(true)->save($data);
            if($id){
                $success_count++;
            }else{
                $fail_counts++;
                //导入失败的元文件行号/错误原因
                $fail_array[] = ['key'=>$key+2,'msg'=>$teacher->getError()];
            }
        }
        return Util::successJsonReturn(['msg' => '上传成功,所有教师默认密码为t123456','success_counts'=>$success_count,'fail_counts'=>$fail_counts,'fail_array'=>$fail_array]);
    }

    public function excelUpload(){
        $base = request()->root();
        $root = strpos($base, '.') ? ltrim(dirname($base), DS) : $base;

        $file = request()->file('file');
        if($file){
            $info = $file->rule('uniqid')->move(ROOT_PATH  . 'public' . DS  . 'static' .DS . 'uploads' . DS . 'excel');
            if ($info) {//上传成功
                $file_name = $info->getFilename();
                $file_url = $root . DS . 'uploads' . DS . 'layedit' . DS .$file_name;
                return Util::successArrayReturn(['msg' => '上传成功','file_url'=>$file_url,'file_name'=>$file_name]);
            }else{//上传失败
                return Util::errorArrayReturn(['msg'=>'上传失败']);
            }
        }else{
            return Util::errorArrayReturn(['msg' => '文件不能为空']);
        }
    }
}