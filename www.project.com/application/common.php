<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
//加密
if(!function_exists('encrypt_password')){
    function encrypt_password($password){
        //加盐
        $salt = "12udhfakjdfad";
        //返回加密的密码
        return md5(md5($password).$salt);
    }
}

//封装无限极分类函数
if (!function_exists('getTree')) {
    function getTree($list,$pid=0,$level=0){
        //定义一个空数组
       static $tree = array();
       //循环$list
       foreach ($list as $row) {
          if ($row['pid'] == $pid) {
              $row['level'] = $level;
              //如果有二级分类,将继续查找,知道没有子类为止,将数据保存到数组中
              $tree[] = $row;
              //继续调用getTree函数
              getTree($list,$row['id'],$level+1);
          }
       }
       return $tree;
    }
}

//无限极分类
if (!function_exists('get_cate_tree')) {
   function get_cate_tree ($list,$pid=0){
      $tree = [];
      foreach ($list as $row) {
        if ($row['pid'] == $pid) {
          $row['son'] = get_cate_tree($list,$row['id']);
          $tree[] = $row;
        }
      }
      return $tree;
   }
}

//使用curl_request向第三方发送请求
if(!function_exists('curl_request')){
    //使用curl发送请求
    function curl_request($url, $post=false, $param=[], $https = false)
    {
        //curl_init 初始化
        $ch = curl_init($url);
        //curl_setopt 设置一些请求选项
        if($post){
            //设置请求方式和请求参数
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }
        // https请求，默认会进行验证
        if($https){
            //禁止从服务器端 验证客户端的证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        //curl_exec 执行请求会话（发送请求）
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        //curl_close 关闭请求会话
        curl_close($ch);
        return $res;
    }
}


//封装一个发送短信的方法
if (!function_exists('sendmsg')) {
    function sendmsg($phone,$msg){
       //接口地址 和 appkey
      $gateway = config('msg.gateway');
      $appkey = config('msg.appkey');
      //拼接地址,发送请求
      $url = $gateway .'?appkey='.$appkey;
      //发送get请求
      $url .= '$phone='.$phone.'&$content='.$msg;
      //发送请求
      $res = curl_request($url,false,[],true);
      //return $res;
      if(!$res){
        //请求发送失败
        return false;
      }
      $arr = json_decode($res,true);
      if ($arr['code'] !=10000) {
        //短信发送失败
        return false;
      }
      return true;
    }
}

//发送邮件
if(!function_exists('sendmail')){
    function sendmail($email, $subject, $body)
    {
        //实例化PHPMailer类
        $mail = new \PHPMailer\PHPMailer\PHPMailer(); // 默认不使用抛出异常的机制 不传参数

        //服务端设置
        //$mail->SMTPDebug = 2;                                 // 调试输出 默认无输出 0
        $mail->isSMTP();                                      // 使用SMTP服务
        $mail->Host = config('email.host');                 // 设置邮件服务器地址
        $mail->SMTPAuth = true;                               // 开启SMTP认证
        $mail->Username = config('email.email');                 // 发件箱账号
        $mail->Password = config('email.password');              // 授权码
        $mail->SMTPSecure = 'tls';                            // 加密方式 TLS  `ssl`
        $mail->Port = 25;                                    // 邮件服务端口  25 发送邮件
        $mail->CharSet = 'utf-8';                           //通用的字符编码

        //客户端设置
        $mail->setFrom(config('email.email'));              //发件人
        $mail->addAddress($email);     // 收件人
        //$mail->addAddress('ellen@example.com');               // Name is optional
//        $mail->addReplyTo('info@example.com', 'Information');   //回复人
//        $mail->addCC('cc@example.com');                         //抄送人
//        $mail->addBCC('bcc@example.com');                       //密送人

        //Attachments
//        $mail->addAttachment('/var/tmp/file.tar.gz');         // 附件
//        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

        //Content
        $mail->isHTML(true);                                  // 设置邮件内容格式为html
        $mail->Subject = $subject;
        $mail->Body    = $body;
//        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $res = $mail->send();
        if($res){
            return true;
        }else{
            return $mail->ErrorInfo;
        }
    }
}

//对手机号进行优化
if (!function_exists('encrypt_phone')) {
   function encrypt_phone ($phone){
     return substr($phone, 0,3) . '****' . substr($phone,7);
   }
}