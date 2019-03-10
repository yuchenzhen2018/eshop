<?php

namespace app\home\controller;

use think\Controller;
use think\Request;

class Cart extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
  public function index()
    {
        //获取购物车的数据
        $list = \app\home\model\Cart::getAllCart();
        //循环遍历每一条数据
        foreach($list as &$v){
        //查询商品基本信息,放到$v中,新加一个goods下标
            $v['goods'] = \app\home\model\Goods::find($v['goods_id']);
            //查询商品选中的属性名称和属性值
            $v['goodsattr'] = \app\home\model\GoodsAttr::getAttrByIds($v['goods_attr_ids']);
        }
        // dump($list);die;
        return view('index',['list'=>$list]);
    }
     
     public function addcart()
     {
         //判断是否时get请求
         if (request()->isGET()){
             //get请求,禁止访问,跳转到首页
             $this->redirect('home/index/index');
         }
         //post请求,接收数据
         $data = request()->param();
         //参数检测格式 略
        // 将数据加入到购物车
        \app\home\model\Cart::addCart($data['goods_id'],$data['number'],$data['goods_attr_ids']);
        //成功后,显示到提示页面
         //查询商品相关信息
         $goods = \app\home\model\Goods::find($data['goods_id']);
         //查询属性名称属性值信息
         $attrs = \app\home\model\GoodsAttr::getAttrByIds($data['goods_attr_ids']);
         return view('addcart',['goods'=>$goods,'number'=>$data['number'],'attrs'=>$attrs]);
     }
      /**
       * 
       */
     public function changenum()
     {
         //接收参数
         $data = request()->param();
         //检验参数
         //处理数据
         \app\home\model\Cart::changeNum($data['goods_id'],$data['goods_attr_ids'],$data['number']);
         //返回数
         $res = [
             'code'=>10000,
             'msg' =>'success'
         ] ;
         return json($res);
     }

     /**
      * 删除ajiax请求的数据
      */
      public function delcart()
      {
          //接收参数
          $data = request()->param();
          //参数检验
          //数据处理
          \app\home\model\Cart::delcart($data['goods_id'],$data['goods_attr_ids']);
          //返回数据
          $res = [
              'code'=>10000,
              'msg' =>'success'
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
