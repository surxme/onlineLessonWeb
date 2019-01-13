<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/5
 * Time: 20:53
 */

namespace app\admin\model;


use think\Model;

class Dept extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp=true;
    protected $createTime='create_time';
    protected $updateTime='update_time';

    /**
     * @param array $default_choosed_arr
     * @return array
     * @throws \think\exception\DbException
     */
    public static function getDept($default_choosed_arr=[]){
        $dept = Dept::all();
        $data_arr = [];

        //所有部门
        foreach ($dept as $data) {
            $data_arr[] = array(
                'value' => $data['id'],
                'title' => $data['name'],
                'parent_id' => $data['p_id'],
                'disabled' => true,
                'data' => []
            );
        }
        //所有教师
        $teacher_arr = Teacher::all();
        foreach ($teacher_arr as $data) {
            foreach ($dept as $k=> $item){
                if($item['id'] == $data['dept_id']){
                    $data_arr[$k]['data'][] = array(
                        'value' => $data['id'],
                        'title' => $data['name'],
                        'dept_id' => $data['dept_id'],
                        'data' => [],
                        'checked' => in_array($data['id'],$default_choosed_arr)?true:false,
                    );
                }
            }
        }
        $top_dept = [];

        //顶级部门
        foreach ($data_arr as $k => $item) {
            if($item['parent_id'] == 0){
                $top_dept[] = $item;
            }
        }

        $data_bak = $data_arr;
        //划分本书从属关系
        foreach ($data_arr as $k => $item){
            foreach ($data_bak as $j => $value){
                if($value['parent_id'] == $item['value']){
                    $data_arr[$k]['data'][] = $value;
                }
            }
        }

        foreach ($top_dept as $k => $value){
            foreach ($data_arr as $j => $item){
                if($value['value'] == $item['parent_id']){
                    $top_dept[$k]['data'][] = $item;
                }
            }
        }
        return $top_dept;
    }

    public static function getDeptTree($default_choosed_arr=[]){
        $dept = Dept::all();
        $data_arr = [];

        //所有部门
        foreach ($dept as $data) {
            $data_arr[] = array(
                'id' => $data['id'],
                'name' => $data['name'],
                'parent_id' => $data['p_id'],
                'children' => []
            );
        }
        $top_dept = [];
        //顶级部门
        foreach ($data_arr as $k => $item) {
            if($item['parent_id'] == 0){
                $top_dept[] = $item;
            }
        }

        $data_bak = $data_arr;
        //划分本书从属关系
        foreach ($data_arr as $k => $item){
            foreach ($data_bak as $j => $value){
                if($value['parent_id'] == $item['id']){
                    $data_arr[$k]['children'][] = $value;
                }
            }
        }

        foreach ($top_dept as $k => $value){
            foreach ($data_arr as $j => $item){
                if($value['id'] == $item['parent_id']){
                    $top_dept[$k]['children'][] = $item;
                }
            }
        }
        return $top_dept;
    }
}