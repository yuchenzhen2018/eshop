<?php

namespace app\home\controller;

use think\Controller;
use think\Request;

class Login extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function login()
    {
        $this->view->engine->layout(false);
        return view();
    }
    public function register ()
    {
        //关闭全局布局
        $this->view->engine->layout(false);
        return view();
    }
    /*
    手机号注册的全过程
    1.1 给发送验证码绑定点击事件,发送Ajax请求到sendmsg
    1.2 接收判定数据,调用发送短信函数(函数内部调用curl链接第三方);,将编辑好的短息格式发送给第三方,让第三方发送
    1.3 返回结果处理,如果发送成功,则缓存验证码和手机号,以及发送的时间
    
    2.1 前台验证数据 提交表单
    2.2 接收数据 后台验证数据 制定验证规则,检测数据 
    2.3 验证码验证 验证码验证完成后,让验证码失效
    2.4 对密码进行加密 ,将is_check的状态改为1
    2.5 加入到数据表 跳转到登陆页面 
     */
    //手机号注册
    public function phone()
    {
          /* 1 接收数据 检测数据
           2 生成验证码,编辑短信内容,发送短信
           3 然后将验证码和手机号一起放到Cooke中,等到登陆是一起验证
           4 设置发送的频率
        */
       $data = request()->param();
       //指定规则
       $rule = [
         'phone'=>'require|regex:^1[3-9]\d{9}$|unique:user',
         'code' =>'require|regex:^\d{4}$',
         'password'=>'require|length:6,16|confirm:repassword'
       ];
       //错误信息
       $msg = [
          'phone.require'=>'手机号不能为空',
          'phone.regex'=>'手机号格式不正确',
          'phone.unique'=>'手机号不能重复',
          'code.request'=>'验证码不能为空',
          'code.regex' =>'验证码长度为4',
          'password.require'=>'密码长度为6到16',
          'password.confirm'=>'两次密码必须一致'
       ];
       //检测
       $validate = new \think\Validate($rule,$msg);
       if(!$validate->check($data)){
           $error = $validate->getError();
           $this->error($error);
       }
       //验证码检测
       //从缓存中获取验证码
       $code = cache('register_code'.$data['phone']);
       if($data['code'] != $code){
          $this->error('验证码错误');
       }
       //验证码验证后即失效
       cache('register_code'.$data['phone'],null);
       //对密码进行加密
       $data['password'] = encrypt_password($data['password']);
       //将is_checked 的状态改为1
       $data['is_check'] = 1 ;
       //将数据保存到数据库中
       \app\home\model\User::create($data,true);
       //注册成功,页面跳转
       $this->success('注册成功,请登陆','login');
    }

    //发短型验证吗
    public function sendmsg()
    {
        //接收手机号
        $phone = request()->param('phone');
        // dump($phone);die;
        //检测参数
        if (!preg_match('/^1[3-9]\d{9}$/', $phone)) {
            //请正确输入手机号吗
            $res = [
               'code'=>10001,
               'msg' =>'参数错误'
            ];    
            return json($res);
        }
        //发送短信不能太频繁
        $last_time = cache('register_sendtime'.$phone) ? :0 ;
        if(time()-$last_time < 60){
            $res = [
               'code'=>10003,
               'msg'=>'发送太频繁',
           ];
           return json($res);
        }
        //处理参数,发送短信
        //生成验证吗
        $code = mt_rand(1000,9999);
        $msg ='【懿夕网络】验证码为：$code,欢迎注册平台！';
        //调用函数,发送短信
        // $result = sendmsg($phone,$msg);
        $result = true;
        if($result == true){
            //将验证码记录到缓存中
            cache('register_code'.$phone,$code,300);
            //记录发送短信的时间
            cache('register_sendtime'.$phone,time());
            $res = [
              'code'=>10000,
              'msg' =>'短信发送成功',
              'data'=>$code
            ];
            return json($res);
        }else{
            $res = [
               'code'=>10002,
               'msg'=>'短信发送失败',
            ];
            return json($res);
        }
    }

    //邮箱注册
    public function email()
    {
        $this->view->engine->layout(false);
        return view();
    }
    /*
    邮箱注册的详细步骤
    下载所需要的插件和登陆邮箱,修改设置里密码登陆的授权,码配置配置文件
    1.1 前台做初步的检测 接收数据 检测数据 提交表单
    1.2 后台接收数据 检测数据 
    1.3 生成一个验证码 将接收到的数据和生成的验证码一同存入user表中
    1.4 利用sendmail邮件给用户 $url和主题以及body
    1.5 判定发送的结果,跳转响应的界面
    2.1 接收传入的数据 数据判定
    2.2 根据id和验证码查找相应的用户
    2.3 如果查到该用户,将用户的is_check的值改为1,重新保存到数据表中 进行页面跳转
     */
    public function registeremail(){
        //接收数据
        $data = request()->param();
        //数据检测
        $rule = [
            'email'=>'require|email|unique:user',
            'password'=>'require|length:6,16|confirm:repassword'
        ];
        //信息提示
        $msg = [
           'email.require'=>'邮箱不能为空',
           'email.email'=>'邮箱格式不正确',
           'email.unique'=>'用户名不能重复呀',
           'password.require'=>'密码不能为空',
           'password.length' =>'密码长度为6到16',
           'password.confirm'=>'两次密码必须一致'
        ];
        //数据检测
        $validate = new \think\Validate($rule,$msg);
        if (!$validate->check($data)) {
            $error = $validate->getError();
            $this->error($error);
        }
        //将数据存到数据表中
        //密码加密
        $data['password'] = encrypt_password($data['password']);
        $data['username'] = $data['email'];
        $data['email_code'] = mt_rand(1000,9999);
        $user = \app\home\model\User::create($data,true);
        //组装信息,给用户发送激活邮件
        $email = $data['email'];
        $subject = "品优购商城注册";
        $url = url('www.project.com/home/login/jihuo',['id'=>$user->id,'code'=>$data['email_code']],true,true);
        $body = "欢迎注册品优购商城,请点击<a href='$url'>点我激活</a>进行激活";
        //发送激活请求
        $res = sendmail($email,$subject,$body);
        if ($res) {
           $this->success('发送激活邮件成功','login');
        }else{
            $this->error('发送激活邮件失败,请联系客服');
        }
    }

    //激活邮件/
    
    public function jihuo()
    {
        //结收数据
        $data = request()->param();
        //根据id和验证吗信息,查找数据
        $user = \app\home\model\User::where(['id'=>$data['id'],'email_code'=>$data['email_code']])->find();
        //如果找到了,将is_check改为1,进行保存
        if($user){
            $user->is_check = 1;
            $user->save();
            $this->success('激活成功','login');   
        }else{
            $this->error('激活失败');
        }
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
     * @param  \think\require  $request
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
    public function dologin()
    {
        //接收数据
        $data = request()->param();
        //数据检测 略
        //查询数据因为用户有两种登陆方法,所以要全部都查询出来
        $password = encrypt_password($data['password']);
         $user = \app\home\model\User::where(function($query)use($data){
            $query->where('email', $data['username'])->whereOr('phone', $data['username']);
        })->where('password', $password)->where('is_check', 1)->find();
        //用户判断
        if ($user) {
            //保存用户信息到session
            session('user_info',$user->toArray());
            //调用Cart模型cookieTodb方法,迁移购物车的数据到数据表
            \app\home\model\Cart::cookieTodb();
            //先从session中获取跳转地址
            $back_url = session('back_url') ? : 'home/index/index';
            //页面跳转
            $this->success('登陆成功',$back_url);
        } else {
            $this->error('登录失败');
        }
        
    }

    public function layout()
    {
        //清空session
        session(null);
        //页面跳转到登陆页
        $this->redirect('/home/index/index');
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
    public function qqcallback($id)
    {
        require_once './plugins/qq/API/qqConnectAPI.php';
        $qc = new \QC();
        $access_token = $qc->qq_callback();
        $openid = $qc->get_openid();
        // dump($access_token);
        // dump($openid);die;
        
    }
}
