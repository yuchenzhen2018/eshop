<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Manager extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //接收要查找的数据
        $search = request()->param('search');
        $this->assign('search',$search);
        // dump($search);die;
        //展示数据
       $manager = \app\admin\model\Manager::where('username','like',"%{$search}%")->order('id desc')->paginate(4);
       // dump($manager);die;
        return view('index',['manager'=>$manager]);
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
        /*1 接收数据
          2 数据判断
          3 数据添加入库*/
          //接收数据
          $data = $request->param();
          // dump($data);die;
          $username = $data['username'];
          $nickname = $data['nickname'];
          $password = $data['password'];
          $email = $data['email'];
          // //数据判定
          $rule = [
            'username'=>'require|length:1,8',
            'nickname'=>'require',
            'password'=>'require|length:6,8',
            'email' =>'require|/^[a-z]+@[a-z]+\.[a-z]{2,3}$/'
          ];
          // //信息
          $msg = [
             'username.require'=>'用户名不能为空',
             'username.length' =>'用户名长度必须在1到8个',
             'nickname'=>'别名不能为空',
             'password.require'=>'密码不能为空',
             'password.length'=>'密码长度在6到8',
             'email.require'=>'邮箱不能为空',
             'email./^[a-z]+@[a-z]+\.[a-z]{2,3}$/'=>'邮箱格式不正确'
          ];
          $validate = new \think\Validate($rule,$msg);
          // //数据检测
          if (!$validate->check($data)) {
              $error = $validate->getError();
              $this->error($error);
          }
          //调用加密行数,对密码进行加密
          $data['password'] = encrypt_password($data['password']);
          //将数据添加到数据表中
          \app\admin\model\Manager::create($data,true);
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
        /*
        根据id查出数据
        然后把数据展示到页面上
         */
        $manager = \app\admin\model\Manager::find($id);
        return view('edit',['manager'=>$manager]);
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
        /*接收数据
          判定数据
          做更新操作
          页面跳转
        */
       //接收修改的数据
       $data = $request->param();
        // //数据判定
          $rule = [
            'username'=>'require|length:1,8',
            'nickname'=>'require',
            'email' =>'require'
          ];
          // //信息
          $msg = [
             'username.require'=>'用户名不能为空',
             'username.length' =>'用户名长度必须在1到8个',
             'nickname'=>'别名不能为空',
             'email.require'=>'邮箱不能为空',
          ];
          $validate = new \think\Validate($rule,$msg);
          // //数据检测
          if (!$validate->check($data)) {
              $error = $validate->getError();
              $this->error($error);
          }
          //数据做更新操作
          \app\admin\model\Manager::update($data,['id'=>$id],true);
          //页面跳转
          $this->success('操作成功','index');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //id检测
        if(!preg_match('/^\d+$/', $id) || $id == 0){
            $this->error('参数错误');
        }
        //执行删除操作
        \app\admin\model\Manager::destroy($id);
        //页面跳转
        $this->redirect('index');
    }

    // public function repassword(){

    // }
}


