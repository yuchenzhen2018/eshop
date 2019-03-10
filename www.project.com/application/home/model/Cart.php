<?php

namespace app\home\model;

use think\Model;

class Cart extends Model
{
    //加入购物车
    public static function addCart($goods_id,$number,$goods_attr_ids)
    {
        //判断是否登陆,一登录,添加到数据表中,未登录,放到还存中
        if (session('?user_info')){
            //一登陆,添加数据到数据表中
            //判断是否存在相同的记录(同一个商品,属性一样,同一个用户登陆)
            $user_id = session('user_info.id');
            //查询条件
            $where = [
                'user_id'=>$user_id,
                'goods_id'=>$goods_id,
                'goods_attr_ids'=>$goods_attr_ids
            ];
            $data = self::where($where)->find();
            if ($data){
                //存在相同记录,则修改原纪录,累加购买商品数量
                $data->number +=$number;
                $data->save();
            }else{
                //不存在相同记录直接添加新纪录
                $where['number'] = $number;
                self::create($where);
            }
        }else{
            //未登录,添加到cookie
            $data = cookie('cart') ? unserialize(cookie('cart')) : [];
            //判断是否存在相同记录(同一个商品,属性值一样)
            //拼接当前要操作的数据 下标 goods__id-goods_attr_ids
            $key = $goods_id.'-'.$goods_attr_ids;
            if (isset($data[$key])){
                //存在相同的记录,则修改原纪录,累加数量
                $data[$key] += $number;
            }else{
                //不存在相同记录,则添加新纪录
                $data[$key] = $number;
            }
            //讲修改后的数组,从新保存到cookie
            cookie('cart',serialize($data),86400*7);
        }
        return true;
    }

    //获取购物车的数据
    public static function getAllCart()
    {
        //如果是登陆状态,则从数据表中获取,如果未登录状态,则在cookie中获取
        if (session('?user_info')){
            //已经登陆了,直接在数据库中查询
            $user_id = session('user_info.id');
            //查询购物车里面的物品
            $data = self::where('user_id',$user_id)->select();
            // var_dump($data);die;
            //将数据转化为标准的二维数组
            foreach($data as &$v){
                $v = $v->toArray();
            }
        }else{
           // 未登录状态
            $cart_data = cookie('cart') ? unserialize(cookie('cart')) : [];
            // dump($cart_data);die;
            //转化数组结构,转化标准的二维数组结构
            $data = [];
            foreach ($cart_data as $k=>$v) {
                //将$k用-分割为数组
                $temp = explode('-',$k);
                //组装一条数据 一维数组
                $row = [
                  'id'=>'',
                  'goods_id'=>$temp[0],
                  'goods_attr_ids'=>$temp[1],
                  'number' =>$v
                ];
                //将组装的数据 放到结果数组中
                $data[] = $row;
            }
        }
        return $data;
    }
    //迁移cookie购物车数据到数据表
    public static function cookieTodb()
    {
        //从cookie中取出所有的购物车数据,逐条添加到数据表中
        //从cookie中取出所有的数据
        $data = cookie('cart') ? unserialize(cookie('cart')) : [] ;
        //遍历数组,对每一条数据(每个键值对)
        foreach ($data as $k => $v) {
            // $k goods_id-goods_attr_ids
            $temp = explode('-', $k);
            $goods_id = $temp[0];
            $goods_attr_ids = $temp[1];
            $number = $v;
            //由于当前cookieTodb方法,是在登陆之后才调用
            //这里加入购物车数据表可以直接调用addcart方法
            self::addCart($goods_id,$number,$goods_attr_ids);
        }
        //清除cookie中的购物车数据
        cookie('cart',null);
    }
    //修改指定购物记录的购买数量
    public static function changeNum($goods_id,$goods_attr_ids,$number)
    {
        //判断是否登陆,如果登陆修改数据表,未登录,修改cookie
        if (session('?user_info')) {
            //已登陆,修改数据表
            $user_id = session('user_info.id');
            //修改条件 用户id,商品id 属性值ids一起作为条件
            $where = [
                'user_id'=>$user_id,
                'goods_id'=>$goods_id,
                'goods_attr_ids'=>$goods_attr_ids
            ];
            //修改数据表
            // self::update(['number'=>$number],$where)
            self::where($where)->update(['number'=>$number]);
        }else{
            //未登录,修改cookie中的数据
            $data = cookie('cart') ? unserialize(cookie('cart')) : [];
            //拼接当前操作数据的下标
            $key = $goods_id . '-' .$goods_attr_ids;
            //修改购买数量
            $data[$key] = $number;
            //将修改后的数组,重新保存到cookie中
            cookie('cart',serialize($data),86400*7);
        }
        return true;
    }
    //删除购物车记录
    public static function delCart($goods_id,$goods_attr_ids)
    {
       //判断是否登陆,如果登陆则在数据表中删除,未登录,在cookie中删除
       if (session('?user_info')) {
           //已登陆,从数据表中删除
           $user_id = session('user_info.id');
           //删除条件
           $where = [
               'user_id'=>$user_id,
               'goods_id'=>$goods_id,
               'goods_attr_ids'=>$goods_attr_ids
           ];
           //执行删除
           self::where($where)->delete();
       }else{
           //未登录,在cookie中删除
           $data = cookie('cart') ? unserialize(cookie('cart')) : [];
           //拼接要删除的下标
           $key = $goods_id .'-'.$goods_attr_ids;
           //从数组中删除记录
           unset($data[$key]);
           //将新的数组重新保存到cookie中
           cookie('cart',serialize($data),86400*7);
       }
       return true;
    }
}
