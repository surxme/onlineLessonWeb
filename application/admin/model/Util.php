<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/5
 * Time: 11:59
 */

namespace app\admin\model;


class Util
{
    public static function successJsonReturn($arr = []){
        $data_json = [
            'is_ok' => 'ok',
            'msg' => '操作成功'
        ];

        $data_json = array_merge($data_json,$arr);
        return json_encode($data_json);
    }

    public static function successArrayReturn($arr = []){
        $data_arr = [
            'is_ok' => 'ok',
            'msg' => '操作成功'
        ];

        $data_arr = array_merge($data_arr,$arr);
        return $data_arr;
    }

    public static function errorArrayReturn($arr=[]){
        $data_arr = [
            'is_ok' => 'error',
            'msg' => '操作失败'
        ];

        $data_arr = array_merge($data_arr,$arr);
        return $data_arr;
    }

    public static function errorJsonReturn($arr=[]){
        $data_json = [
            'is_ok' => 'error',
            'msg' => '操作失败'
        ];
        $data_json = array_merge($data_json,$arr);
        return json_encode($data_json);
    }
    /**
     * @param $arr
     * @return string
     * {
     *   "code": 0 //0表示成功，其它失败
     *   ,"msg": "" //提示信息 //一般上传失败后返回
     *   ,"data": {
     *   "src": "图片路径"
     *   ,"title": "图片名称" //可选
     *   }
     * }
     */

    public static function layeditJsonReturn($arr){
        $data_json = [
            'code' => '0',
            'msg' => '操作成功',
            'data' => [
                'src' => '',
                'title' => ''
            ]
        ];
        $data_json = array_merge($data_json,$arr);
        return json_encode($data_json);
    }
}