<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Type extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //查询并展示数据
        $data = \app\admin\model\Type::select();
        //dump($data);die;
        return view('index',['data'=>$data]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return view();
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        /*添加商品类型
        1 接收数据
        2 检测数据
        3 数据入库*/
        //接收数据
        $data = $request->param();
        //数据检测
        if (empty($data['type_name'])) {
            $this->error('商品类型名称不能为空');
        }
        //添加数据到数据表
        \app\admin\model\Type::create($data,true);
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
        //将数据展示到页面上
        //获取所有数据
        $type = \app\admin\model\Type::find($id);
        return view('edit',['type'=>$type]);
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
      
        //数据判定
        if (empty($id)) {
            $res = [
               'code'=>10001,
               'msg'=>'参数错误'
            ];
            return json($res);
        }
    
         //查询型的数据
         $data = \app\admin\model\Type::find($id);
        // dump($data);die;
        //返回数据
        $res =[
           'code'=>10000,
           'msg'=>'请求成功',
           'data'=>$data
        ];
        return json($res);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //根据id进行删除
        \app\admin\model\Type::destroy($id);
        //跳转
        $this->success('删除成功','index');
    }
}
