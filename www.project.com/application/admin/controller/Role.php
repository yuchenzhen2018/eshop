<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Role extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //获取数据并展示数据
        $role = \app\admin\model\Role::order('id desc')->paginate(4);
        return view('index',['role'=>$role]);
    }
    public function setauth($id){
       //根据id获取角色信息
        $role = \app\admin\model\Role::find($id);
        //查询一级分类
        $top_auth = \app\admin\model\Auth::where('pid',0)->select();
        // dump($top_auth);die;
        //查询二级分类
        $sec_auth = \app\admin\model\Auth::where('pid','>',0)->select();
        return view('setauth',['role'=>$role,'top_auth'=>$top_auth,'sec_auth'=>$sec_auth]);
    }
    public function saveauth (){
        /*保存分配的权限
        1 接收数据
        2 数据检验
        3 数据入库
        4 跳转
        */ 
        //接收数据 分别接收角色id 和权限id
        $role_id = request()->param('role_id');
        //权限id
        $auth_id = request()->param('id/a');
        //  dump($auth_id);die;
        //数据检测 略
        //把所有的权限id组成一个字符串
        $role_auth_ids = implode(',',$auth_id);
        // dump($role_auth_ids);die;
        //将数据添加到role表中
        \app\admin\model\Role::update(['id'=>$role_id,'role_auth_ids'=>$role_auth_ids]);
        //页面跳转
        $this->success('操作成功','index');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
