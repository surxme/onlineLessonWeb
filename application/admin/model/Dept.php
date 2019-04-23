<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/5
 * Time: 20:53
 */

namespace app\admin\model;


use think\Db;
use think\Model;

class Dept extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp=true;
    protected $createTime='create_time';
    protected $updateTime='update_time';

    public function search($params){
        $where = [];
        $list = Db::name('dept')->alias('t');

        if(isset($params['search_key'])){
            $where['name'] = array('like','%'.$params['search_key'].'%');
        }
        $field = 't.*';

        $list =$list->where($where)->order('id desc')->field($field)->paginate(10);

        return $list;
    }
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
    /**
     * @param array $default_choosed_arr
     * @return array
     * @throws \think\exception\DbException
     */
    public static function getDept1($default_choosed_arr=[]){
        $dept = Dept::all();
        $data_arr = [];
        $teachers = [];

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
        $dept_tree = self::list_to_tree($data_arr,'value','parent_id','data');

        //所有教师
        $teacher_arr = Db::name('teacher')->where('is_del',0)->select();

        foreach ($teacher_arr as $teacher) {
            $teachers[] = array(
                'value' => $teacher['id'],
                'title' => $teacher['name'],
                'dept_id' => $teacher['dept_id'],
                'disabled' => in_array($teacher['id'],$default_choosed_arr)?false:true,
                'data' => []
            );
        }

        $res = self::getTeacherTree($dept_tree,$teacher_arr,$default_choosed_arr);
        return $res;
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
        $dept_tree = self::list_to_tree($data_arr,'id','parent_id','children');
        return $dept_tree;
    }

    /**
     * @param $list
     * @param string $pk
     * @param string $pid
     * @param string $child
     * @param int $root
     * @return array
     */
    public static function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'children', $root = 0)
    {
        // 创建Tree
        $tree = array();
        if (is_array($list)) {
            // 创建基于主键的数组引用
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                        // $parent['children'][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }

    public static function getTeacherTree(&$dept_tree,$teacher_arr,$default_choosed_arr = []){
        foreach ($dept_tree as $k=> $dept_item){
            foreach ($teacher_arr as $teacher){
                if($dept_item['value'] == $teacher['value']){

                }

            }
        }

        return $dept_tree;
    }
}