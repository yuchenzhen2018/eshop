<?php
namespace app\home\controller;

class Index extends Base
{
    public function index()
    {
    	//查询数据到页面
    	$goods = \app\admin\model\Goods::select();
    	// dump($goods);die;
        return view('index',['goods'=>$goods]);
    }
}
