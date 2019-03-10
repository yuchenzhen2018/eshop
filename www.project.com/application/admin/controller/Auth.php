<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Auth extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //获取信息,并展示数据到页面上
        $auth = \app\admin\model\Auth::select();
        //使用递归函数,重新排序
        $auth = getTree($auth);
        //展示到页面上
        return view('index',['auth'=>$auth]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //展示一级分类
        $top_auth = \app\admin\model\Auth::select();
        return view('create',['top_auth'=>$top_auth]);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //接收数据,将数据存到数据表中
        $data = $request->param();
        $auth = \app\admin\model\Auth::create($data,true);
        //页面跳转
        $this->success('操作成功','index');
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
        return view();
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
        //检测id
        if(!preg_match('/^\d+$/',$id)){
             $this->error('参数错误');
        }
        //进行删除操作
        \app\admin\model\Auth::destroy($id);
        $this->success('操作成功','index');
    }
}
