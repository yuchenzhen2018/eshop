<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Attribute extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //接收数据并渲染数据 需要商品类型表的商品类型名称
        $data = \app\admin\model\Attribute::alias('t1')
            ->field('t1.*,t2.type_name')
            ->join('tpshop_type t2','t1.type_id =t2.id','left')
            ->select();
        return view('index',['data'=>$data]);
    }

    public function getattr($type_id){
         //dump($type_id);die;
        //根据type_id查询属性值
        $data = \app\admin\model\Attribute::where('type_id',$type_id)->select();
        //对每一条数据,取原始数据(没有经过获取器转化过的数据)
        foreach($data as &$v){
            $v = $v->getData();
            //将可选值 分割为数组
            $v['attr_values'] = explode(',',$v['attr_values']);
        }
        unset($v);
        //返回数据
        //dump($data['']);die;
        $res = [
            'code'=>10000,
            'msg' =>'请求成功',
            'data'=>$data
        ];
        return json($res);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //查询所有的商品类型,用下拉列表展示
        $type = \app\admin\model\Type::select();
        return view('create',['type'=>$type]);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //接收数据
        $data = $request->param();
        //数据检测
        //数据添加到数据表中
        \app\admin\model\Attribute::create($data,true);
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
        /*展示数据
        1 根据id获取数据
        2 将数据展示到页面上
        //  */
        // $attribute = \app\admin\model\Attribute::find($id);
        // $type = \app\admin\model\Type::find($id);
        // dump($type);die;
        // return view();
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
        //id的检测 lue

        //执行删除操作
        \app\admin\model\Attribute::destroy($id);
        $this->redirect('index');
    }
}
