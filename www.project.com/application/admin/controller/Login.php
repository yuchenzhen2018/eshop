<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Login extends Controller
{
    public function login()
    {
        //设置密码
        //1 先在公共里面添加一个加盐函数,来获取加密密码
        //2 调用改函数,设密码
        // echo encrypt_password('123456');die;

       /*首先判定时登陆还是展示页面
       登陆
       1 接收数据
       2 判定数据有效性 rule 和 validate
       3 数据入库
       4 页面跳转
        * */
        if(request()->isGet()){
            //如果时get请求,则展示页面
            $this->view->engine->layout(false);
            return view();
        }
        //如果时post请求则执行登陆操作
        //接收数据
       $data = request()->param();
        //制定规则
        $rule = [
            'username'=>'require',
            'password'=>'require|length:6,16',
            'captcha'    =>'require|length:4'
        ];
        $msg =[
            'username.require'=>'用户名不能为空',
            'password.require'=>'密码不能为空',
            'password.length'=>'密码长度范围在6-16',
            'captcha.require'=>'密码不能为空',
            'captcha.length'=>'验证码长度必须为4'
        ];
       //判定
        $validate = new \think\Validate($rule,$msg);
        if(!$validate->check($data)){
            $error = $validate->getError();
            //返回错误信息
            $this->error($error);
        }
        //验证码验证
//        $captcha = $data['captcha'];
//        if(!captcha_check($captcha)){
//             $this->error('验证码错误');
//        }
        //将密码进行加密
        $password = encrypt_password($data['password']);
        //常洵数据
        $user = \app\admin\model\Manager::where(['username'=>$data['username'],'password'=>$password])->find();
        //页面跳转
        if ($user){
            //如果登陆成功,将用户名保存到session中
            session('manager_info',$user->toArray());
            $this->success('登陆成功','admin/index/index');
        }else{
            $this->error('登陆失败');
        }
    }

    /*
     * 退出*/
    public function layout(){
       //清空session即可
        session(null);
        $this->redirect('admin/login/login');
    }

    //修改密码
    public function change($id){
        /*接收新的密码,做更新操作,做加密操作
         * */
        echo $id;

    }
}
