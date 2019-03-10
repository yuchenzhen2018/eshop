<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Category extends Base
{
    public function getsubcate($id)
    {
        //数据判定
      if(empty($id)){
         $res = [
             'code'=>10001,
             'msg' =>'参数错误',
         ];
         return json($res);
      }
       //根据id查询二级分类
       $list = \app\admin\model\Category::where('pid',$id)->select();
        $res = [
          'code'=>10000,
          'msg' =>'请求成功',
          'data' =>$list
        ];
        return json($res);
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        
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
