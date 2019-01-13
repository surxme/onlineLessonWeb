<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/5
 * Time: 15:34
 */

namespace app\admin\model;


use think\Db;
use think\Model;

class Type extends Model
{
    protected $pk = 'id';
    protected $autoWriteTimestamp=true;
    protected $createTime='create_time';
    protected $updateTime='update_time';

    public function search($params){
        $where = [];

        if(isset($params['search_key'])){
            $where['name'] = array('like','%'.$params['search_key'].'%');
        }

        $list = Db::name('type')->where($where)->order('id desc')->paginate(10);

        return $list;
    }
}