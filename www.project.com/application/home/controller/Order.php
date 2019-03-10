<?php

namespace app\home\controller;

use think\Controller;
use think\Request;

class Order extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //  
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //判断是否登陆
        if (!session('?user_info')) {
            //如果没有登陆,跳转到登陆页面
            //设置登陆后 跳转的页面地址
            session('back_url','home/cart/index');
            $this->redirect('home/login/login');
        }
        //接收cart_ids
        $cart_ids = request()->param('cart_ids');
        //用户id
        $user_id = session('user_info.id');
        //查找收货地址
        $address = \app\home\model\Address::where('user_id',$user_id)->select();
        //从配置中获取支付方式
        $pay_type = config('pay_type');
        //查询商品表
        $data = \app\home\model\Cart::alias('t1')
            ->field('t1.*, t2.goods_name, t2.goods_logo, t2.goods_price, t2.goods_number')
            ->join('tpshop_goods t2', 't1.goods_id = t2.id', 'left')
            ->where('t1.id', 'in', $cart_ids)
            ->select();
       //计算总数量和价格
        $total_number = 0;
        $total_price = 0;
        foreach($data as $v){
            // $v['number']  $v['goods_price']
            $total_number += $v['number'];
            $total_price += $v['number'] * $v['goods_price'];
        }
        return view('create', ['address' => $address, 'pay_type' => $pay_type, 'data' => $data, 'total_number' => $total_number, 'total_price' => $total_price]);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
       //先订单表中添加一条数据
       //接收数据
       $data = request()->param();
       //参数检测
       //生成订单号,唯一不重复
       $order_sn = time() . mt_rand(10000000,99999999); 
       //开启事务
       \think\Db::startTrans();
       try {
            //用户id
            $user_id = session('user_info.id');
            //收获地址
            $address  =  \app\home\model\Address::find($data['address_id']);
            //查询购物车表及商品表 连表查询
            $cart_data = \app\home\model\Cart::alias('t1')
                ->field('t1.*, t2.goods_price, t2.goods_name, t2.goods_logo, t2.goods_number')
                ->join('tpshop_goods t2', 't1.goods_id=t2.id', 'left')
                ->where('t1.id', 'in', $data['cart_ids'])
                ->select();
                //库存检测
            //正常清空下  每一种属性值组合的商品 有单独的库存
            /*foreach($cart_data as $v){
                //$v['number']  $v['goods_number']
                if($v['number'] > $v['goods_number']){
                    // $this->error('订单中包含库存不足的商品');
                    throw new \Exception('订单中包含库存不足的商品', 10002);
                }
            }*/
            //当前 一个商品id 对应一个库存
            /*$goods_number = [
                '162' => ['number' => 36, 'goods_number'=> 10000],
                '163' => ['number' => 4, 'goods_number' => 10000]
            ];*/
            //每一个商品 对应一个库存
            $goods_number = [];
            foreach ($cart_data as $v) {
                if (!isset($goods_number[$v['goods_id']])) {
                   //第一次设置某个商品
                   $goods_number[$v['goods_id']] = ['number' => $v['number'], 'goods_number' => $v['goods_number']];
                }else{
                    //继续累加购买数量
                    $goods_number[$v['goods_id']]['number'] += $v['number']; 
                }
            }
            unset($v);
            //逐个检测库存
            foreach($goods_number as $v){
                if ($v['number'] > $v['goods_number']) {
                   throw new \Exception('订单中包含数量不足的商品',10002);
                }
            }
            //累加计算总金额
            $order_amount = 0 ;
            foreach ($cart_data as $v){
                $order_amount +=$v['goods_price']*$v['number'];
            }
            //组装一条订单数据
            $order_data = [
               "order_sn"=>$order_sn,
               "user_id"=>$user_id,
               "order_amount"=>$order_amount,
               "consignee_name"=>$address['consignee'],
               "consignee_phone"=>$address['phone'],
               "consignee_address"=>$address['address'],
               "shipping_type"=>"shunfeng",
               "pay_type"=>$data['pay_type']
            ];
            //向订单表中添加一条数据
            $order = \app\home\model\Order::create($order_data);
            //组装订单商品表的数据 得用到前面查询的购物记录商品信息
            $order_goods_data = [];
            foreach ($cart_data as $v) {
                //组装商品表的一条数据
                $row =[
                  "order_id"=>$order->id,
                  "goods_id"=>$v['goods_id'],
                  "goods_name"=>$v['goods_name'],
                  "goods_price"=>$v['goods_price'],
                  "goods_logo"=>$v['goods_logo'],
                  "number"=>$v['number'],
                  "goods_attr_ids"=>$v['goods_attr_ids']
                ];
                //将一条一条的数据,放到结果数组,用于后面批量添加
                $order_goods_data[] = $row;
            }
            // dump($order_goods_data);die;
            //批量添加到订单商品表
            $ordergoods = new \app\home\model\OrderGoods();
            $res = $ordergoods->saveAll($order_goods_data);
            // dump($res);die;
            //库存预扣减(冻结)
            $goods_data = [];
            foreach ($goods_number as $k => $v) {
             // $k 商品id  $v['number'] 购买数量  $v['goods_number'] 库存
             $row = ['id'=>$k,'goods_number'=>$v['goods_number']-$v['number']];
             $goods_data[] = $row;
            }
            //批量修改商品库存
            $goods = new \app\home\model\Goods();
            $goods->saveAll($goods_data);
            //从购物车表删除对应记录
            \app\home\model\Cart::destroy($data['cart_ids']);
            //提交事务
            \think\Db::commit();
       }catch(\Exception $e){
           //回滚事务
           \think\Db::rollback();
           //错误提示
           $error = $e->getMessage();
           $this->error($error);
       }
       echo "接下来到了支付流程";die;
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
