<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/2/1
 * Time: 20:53
 */

namespace app\index\model;


use think\Db;
use think\Model;

class Teacher extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp=true;
    protected $createTime='create_time';
    protected $updateTime='update_time';

    public function search($params,$pageSize = 10){
        $where = [];
        $list = Db::name('teacher')->alias('t')->order('id desc')->join('t_dept dept','t.dept_id = dept.id','LEFT');

        if(isset($params['dept_ids'])){
            $where['dept_id'] = array('in',implode(',',$params['dept_ids']));
        }

        if(isset($params['search_key'])){
            $where['t.name|teacher_no'] = array('like','%'.$params['search_key'].'%');
        }
        $list = $list->where($where);
        $list = $list->field('t.*,dept.name as dept_name')->paginate($pageSize);

        return $list;
    }

    /**
     * @param $start
     * @param $end
     * @param $uid
     * @param $type
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getEchartData($start,$end,$uid,$type,$name){
        $group_way = "%Y/%c/%e";
        $hits = [];
        if($type == 'thumbs'){
            $hits = Db::name('video_attr')
                ->alias('t')
                ->join('video v','t.video_id = v.id')
                ->where('teacher_id',$uid)
                ->where('t.create_time','>=',$start)
                ->where('t.create_time','<=',$end)
                ->where('t.type',1)
                ->group('FROM_UNIXTIME(t.create_time,"'.$group_way.'")')
                ->order('t.create_time asc')
                ->field('count(*) as counts,FROM_UNIXTIME(t.create_time,"'.$group_way.'") as time_id')
                ->select();
        }else if($type == 'hits'){
            $hits = Db::name('user_behavior')
                ->alias('t')
                ->join('video v','t.data_id = v.id')
                ->where('teacher_id',$uid)
                ->where('t.create_time','>=',$start)
                ->where('t.create_time','<=',$end)
                ->where('t.action_type',2)
                ->group('FROM_UNIXTIME(t.create_time,"'.$group_way.'")')
                ->order('t.create_time asc')
                ->field('count(*) as counts,FROM_UNIXTIME(t.create_time,"'.$group_way.'") as time_id')
                ->select();
        }

        $date_arr = self::getDateNameArr($start,$end);
        self::IsLostData($date_arr,$hits,'time');

        $echart_visit_num = [];
        $mark_point_client_data = [];

        foreach ($hits as $k => $val){
            $item['value'] = $val['counts'];
            $item['xAxis'] = $k;
            $item['yAxis'] = $val['counts'];
            $mark_point_client_data[] = $item;
            $echart_visit_num[] = $val['counts'];
        }

        $echart_data_increment = [
            'name' => $name,
            'type' => 'bar',
            'data' => $echart_visit_num,
            'itemStyle' => [
                'normal' => [
                    //柱形图圆角，初始化效果
                    'barBorderRadius' => [5, 5, 2, 2]
                ]
            ],
            'markPoint' => [
                'data' => $mark_point_client_data
            ],
            'markLine' => [
                'data' => [
                    ['type' => 'average', 'name' => '平均值']
                ]
            ],
        ];

        $xAxis_name_arr = array_keys($date_arr);
//        $xAxis_name_arr = json_encode(array_keys($date_arr));
//        $echart_data_increment = json_encode($echart_data_increment);

        return [$xAxis_name_arr,$echart_data_increment];
    }

    public static function getDateNameArr($time_start, $time_end, $is_flip = true)
    {
        $day_start = date('Y-n-j', $time_start);
        $day_end = date('Y-n-j', $time_end);
        $_time = range(strtotime($day_start), strtotime($day_end), 24 * 60 * 60);
        $_time = array_map(create_function('$v', 'return date("Y/n/j", $v);'), $_time);

        if ($is_flip) {
            $_time = array_flip($_time);
        }
        return $_time;
    }

    private static function IsLostData($compare_arr,&$data_arr,$id_type_str){
        $data_arr_bak = [];

        foreach ($data_arr as $j => $item) {
            $data_arr_bak[$item[$id_type_str.'_id']] = $item;
        }

        $data_arr = [];
        foreach ($compare_arr as $k => $v){
            if(!isset($data_arr_bak[$k])){
                $data_arr_item = [
                    'counts' => '0',
                    $id_type_str.'_id' => ''.$k
                ];

                $data_arr[] = $data_arr_item;
            }else{
                $data_arr[] = $data_arr_bak[$k];
            }
        }
    }
}