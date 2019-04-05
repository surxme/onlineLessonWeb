<?php
/**
 * Created by PhpStorm.
 * User: www44
 * Date: 2019/1/1
 * Time: 20:54
 */

namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\model\Util;
use think\Db;
use think\Validate;

class AdminController extends BaseController
{
    public function  index(){
        $params = input('param.');
        $list = (new Admin())->search($params);
        $this->assign('list',$list);
        return $this->fetch('admin/index');
    }

    /**
     *  添加
     * @throws \think\exception\DbException
     */
    public function add(){
        $id = input('param.id');
        if($id){
            $info = Admin::get($id);
            $this->assign('info',$info);
        }

        return $this->fetch('admin/add');
    }

    /**
     * 保存
     * @return array
     */
    public function saveAdd(){
        $name = input('param.name');
        $id = input('param.id');
        $password = input('param.password');
        $data = [
            'name' => $name,
            'password' => $password
        ];

        $admin = new Admin();

        $msgs = [
            'name.require'=>'未填写用户名',
            'name.max'=>'用户名长度超过10',
            'name.chsDash'=>'用户名只能是汉字、字母、数字和下划线_及破折号-',
            'password.require'=>'密码不能为空',
            'password.min'=>'密码为6-12位',
            'password.max'=>'密码为6-12位'
        ];
        $rules = [
            'name'  => 'require|max:10',
            'password' => 'require|min:6|max:12'
        ];

        if($id){
            $data['id'] = $id;
            if ($password){
                $validate = new Validate($rules,$msgs);
                if (!$validate->check($data)) {
                    return Util::errorArrayReturn(['msg'=>$validate->getError()]);
                }
                $data['password'] = Admin::passwordfix($password);
            }else{
                unset($data['password']);
            }
            $res = $admin->validate(true)->save($data,['id'=>$id]);
        }else{
            $validate = new Validate($rules,$msgs);
            if (!$validate->check($data)) {
                return Util::errorArrayReturn(['msg'=>$validate->getError()]);
            }
            $data['password'] = Admin::passwordfix($password);
            $res = $admin->validate(true)->save($data);
        }
        if($res){
            return Util::successArrayReturn();
        }else{
            return Util::errorArrayReturn(['msg'=>$admin->getError()]);
        }
    }

    /**
     * 删除
     */
    public function del(){
        $id=input('param.id');

        $re= (new Admin())->where('id',$id)->delete();
        if ($re){
            return Util::successArrayReturn();
        }else{
            return Util::errorArrayReturn();
        }
    }
}