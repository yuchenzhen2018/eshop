<?php

namespace app\home\controller;

use think\Controller;
use think\Request;

class Goods extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index($id)
    {
        //根据传过来的id查询类型
        $cate = \app\home\model\Category::find($id);
        //根据cate_id查询商品信息
        $goods = \app\home\model\Goods::where('cate_id',$id)->order('id desc')->paginate(2);
         // dump($goods);die;
        return view('index',['cate'=>$cate,'goods'=>$goods]);
    }

    public function detail($id)
    {
      $goods = \app\home\model\Goods::find($id);
      $goodspics = \app\home\model\Goodspics::where('goods_id',$id)->select();
      //找到type_id
      $type_id = $goods['type_id'];
      //根据type_id查找属性名称()
      $attribute = \app\home\model\Attribute::where('type_id',$type_id)->select();
      // dump($attribute);die;
      //获取当选属性值
      $goodsattr = \app\home\model\GoodsAttr::where('goods_id',$id)->select();
      // dump($goodsattr);die;
      //为页面展示方便,数组进行转化
      $new_goodsattr = [];
      foreach($goodsattr as $v){
        $new_goodsattr[$v['attr_id']][] = $v->toArray();
      }
      // dump($new_goodsattr);die;
      return view('detail',['goods'=>$goods,'goodspics'=>$goodspics,'attribute'=>$attribute,'new_goodsattr'=>$new_goodsattr]);
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
