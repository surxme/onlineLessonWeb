<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/2/17
 * Time: 20:43
 */

namespace app\index\controller;


use app\admin\model\Admin;
use app\admin\model\Student;
use app\admin\model\Teacher;
use app\admin\model\Util;
use PHPExcel_IOFactory;
use think\Db;

class FileUpload extends  BaseController
{
    const IMPORT_TYPE_TEACHER = 1;
    const IMPORT_TYPE_STUDENT = 2;
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

    /**
     * excel导入 教师/学生信息导入
     * @return string
     * @throws \PHPExcel_Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function PhpExcelImport(){

        $type = input('param.type');

        if($type==''||!in_array($type,[FileUpload::IMPORT_TYPE_TEACHER,FileUpload::IMPORT_TYPE_STUDENT])){
            return Util::successJsonReturn(['code' => 1,'msg' => '导入类型有误']);
        }

        $excel = request()->file('excel')->getInfo();//excel为file中的name
        if(!$excel){
            return Util::successJsonReturn(['code' => 1,'msg' => '请先选择需要导入的文件']);
        }
        import('phpexcel.PHPExcel', EXTEND_PATH);
        $objPHPExcel = PHPExcel_IOFactory::load($excel['tmp_name']);//读取上传的文件
        $arrExcel = $objPHPExcel->getSheet(0)->toArray();//获取其中的数据
        array_shift($arrExcel);

        if(empty($arrExcel)){
            return Util::successJsonReturn(['code' => 1,'msg' => '所选择的excel文件内容为空']);
        }

        $success_count = 0;
        $fail_counts = 0;
        $fail_array = array();

        if($type==FileUpload::IMPORT_TYPE_TEACHER){
            self::teacherImport($arrExcel,$success_count,$fail_counts,$fail_array);
        }else{
            self::studentImport($arrExcel,$success_count,$fail_counts,$fail_array);
        }

        return Util::successJsonReturn(['code' => 0,'msg' => '上传成功,所有教师默认密码为t123456','success_counts'=>$success_count,'fail_counts'=>$fail_counts,'fail_array'=>$fail_array]);
    }

    /**
     * 教师信息导入
     * 数据格式：姓名 教师工号 性别 生日 邮箱
     * @param $arrExcel
     * @param $success_count
     * @param $fail_counts
     * @param $fail_array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function teacherImport($arrExcel,&$success_count,&$fail_counts,&$fail_array){
        $default_dept_id = Db::name('dept')
            ->where('p_id=0')
            ->field('id,name')
            ->limit(1)
            ->find()['id'];

        $teacher = new Teacher();

        foreach ($arrExcel as $key => $value) {
            $data = array(
                'name'=>$arrExcel[$key][0],
                'teacher_no'=>$arrExcel[$key][1],
                'password'=>Admin::passwordfix('t123456'),
                'sex' => $arrExcel[$key][2]=='女'?2:1,
                'bir' => strtotime($arrExcel[$key][4]),
                'avatar' => DS . 'uploads' . DS . 'poster'.DS.'logo.svg',
                'email' => $arrExcel[$key][5],
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
                $fail_array[] = ['key'=>$key+2,'teacher_no'=>$data['teacher_no'],'msg'=>$teacher->getError()];
            }
        }
    }

    /**
     * 学生信息导入
     * 数据格式：姓名 学号 性别 生日 邮箱
     * @param $arrExcel
     * @param $success_count
     * @param $fail_counts
     * @param $fail_array
     */
    public function studentImport($arrExcel,&$success_count,&$fail_counts,&$fail_array){
        $student = new Student();

        foreach ($arrExcel as $key => $value) {
            $data = array(
                'name'=>$arrExcel[$key][0],
                'student_no'=>$arrExcel[$key][1],
                'password'=>Admin::passwordfix('s123456'),
                'sex' => $arrExcel[$key][2]=='女'?2:1,
                'bir' => strtotime($arrExcel[$key][4]),
                'avatar' => DS . 'uploads' . DS . 'poster'.DS.'logo.svg',
                'email' => $arrExcel[$key][5],
                'create_time' => time(),
                'update_time' => time()
            );

            $id = $student->validate(true)->save($data);
            if($id){
                $success_count++;
            }else{
                $fail_counts++;
                //导入失败的元文件行号/错误原因
                $fail_array[] = ['key'=>$key+2,'student_no'=>$data['student_no'],'msg'=>$student->getError()];
            }
        }
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