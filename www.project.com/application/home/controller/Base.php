<?php

namespace app\home\controller;

use think\Controller;
use think\Request;

class Base extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function __construct(Request $request)
    {
        //继承父类
        parent::__construct($request);
        //获取所有的分类
        $category = \app\home\model\Category::select();
        // dump($category);die;
        //因为要一条一条的展示到页面上,所以数据变成标准的二维数组
        $category = (new \think\Collection($category))->toArray();
        //调用无限极分类函数
        $category = get_cate_tree($category,$pid=0);
        //展示到页面上
        $this->assign('category',$category);
    }
}
