<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Base extends Controller
{
   //设置权限,只有登陆了才能访问,不然跳转到去登陆页面
    public function __construct(Request $request) {
        //继承父类的初始化
        parent::__construct($request);
        //判定是否登陆,即是否在session中存在
        if (!session('?manager_info')){
             //跳转到登陆页
            $this->redirect('admin/login/login');
        }
        $this->checkauth();
        $this->getnav();
    }

    //菜单选项权限
    public function getnav(){
        //如果是超级管理员,则所有的菜单都显示 如果是普通管理员,只显示它权限岗位内的菜单
        //从session中获取role_id
        $role_id = session('manager_info.role_id');
        // dump($role_id);die;
        if($role_id == 1){
            //超级管理员,拥有所有权限
            //查询所有的一级权限
            $top_nav = \app\admin\model\Auth::where(['pid'=>0,'is_nav'=>1])->select();
            //查询所有的二级权限
            $sec_nav = \app\admin\model\Auth::where(['pid'=>['>',0],'is_nav'=>1])->select();
            // dump($sec_nav);die;
        }else{
            //根据role_id获取role的信息
            $data = \app\admin\model\Role::find($role_id);
            //获取role_auth_ids
            $role_auth_ids = $data['role_auth_ids'];
            //查询一级权限pid=0,有下拉菜单,权限id在role_auth_ids中
            $top_nav = \app\admin\model\Auth::where(['pid'=>0,'is_nav'=>1, 'id'=>['in',$role_auth_ids]])->select();
        // dump($role_auth_ids);die;

            //查询二级权限
            $sec_nav = \app\admin\model\Auth::where(['pid'=>['>',0],'is_nav'=>1,'id'=>['in',$role_auth_ids]])->select();
        }
        //变量赋值
        $this->assign('top_nav',$top_nav);
        $this->assign('sec_nav',$sec_nav);
    }
 
     public function checkauth(){
        //  权限访问控制
        //   如果是超级管理员admin,所有的权限都能看到
        //   如果是普通用户,则只能访问自己权限范围内
        //   首先根据role_id突破口 
        //获取角色id
        $role_id = session('manager_info.role_id');
        //如果是超级管理员,则可以访问任何路径
        if ($role_id == 1 ) return;
        //如果访问的是主页,也可以
        //获取访问的控制器
        $controller = request()->controller();
        //获取要访问的方法
        $action = request()->action();  
        // dump($action);die;
        //如果访问的是主页,则允许访问
        if ( $controller=='Index' && $action=='index') return;
        //一般情况下
        $data = \app\admin\model\Role::find($role_id);
        //后去ids
        $role_auth_ids = $data['role_auth_ids'];
        //转成数组
        $role_auth_ids = explode(',',$role_auth_ids);
        //查询当前访问的id权限
        $auth = \app\admin\model\Auth::where('auth_c',strtolower($controller))->where('auth_c',strtolower($controller))->find();
        //查出访问的权限id
        $auth_id = $auth['id'];
        //判定权限是否在访问的ids里面
        if (!in_array($auth_id,$role_auth_ids)) {
            $this->error('没有权限访问','admin/index/index');
        }
     }
}
