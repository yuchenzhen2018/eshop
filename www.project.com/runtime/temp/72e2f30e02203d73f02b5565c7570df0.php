<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:67:"D:\www.project.com\public/../application/admin\view\goods\edit.html";i:1550815989;s:53:"D:\www.project.com\application\admin\view\layout.html";i:1550986928;}*/ ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="/static/admin/css/main.css" rel="stylesheet" type="text/css" />
    <link href="/static/admin/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/static/admin/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css" />
    <script src="/static/admin/js/jquery-1.8.1.min.js"></script>
    <script src="/static/admin/js/bootstrap.min.js"></script>
</head>

<body>
    <!-- 上 -->
    <div class="navbar">
        <div class="navbar-inner">
            <div class="container-fluid">
                <ul class="nav pull-right">
                    <li id="fat-menu" class="dropdown">
                        <a href="#" id="drop3" role="button" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon-user icon-white"></i> <?php echo \think\Request::instance()->session('manager_info.username'); ?>
                            <i class="icon-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a tabindex="-1" href="<?php echo url('admin/login/change',['id'=>\think\Request::instance()->session('manager_info.id')]); ?>">修改密码</a></li>
                            <li class="divider"></li>
                            <li><a tabindex="-1" href="<?php echo url('admin/login/layout'); ?>">安全退出</a></li>
                        </ul>
                    </li>
                </ul>
                <a class="brand" href="index.html"><span class="first">后台管理系统</span></a>
                <ul class="nav">
                    <li class="active"><a href="javascript:void(0);">首页</a></li>
                    <li><a href="javascript:void(0);">系统管理</a></li>
                    <li><a href="javascript:void(0);">权限管理</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- 左 -->
    <div class="sidebar-nav">
        <?php foreach($top_nav as $k=>$top_v): ?>
        <!-- 锚点跳转 -->
        <a href="#error-menu<?php echo $k; ?>" class="nav-header collapsed" data-toggle="collapse"><i class="icon-exclamation-sign"></i><?php echo $top_v['auth_name']; ?></a>
        <ul id="error-menu<?php echo $k; ?>" class="nav nav-list collapse">
            <?php foreach($sec_nav as $sec_v): if($top_v['id'] == $sec_v['pid']): ?>
            <li><a href="<?php echo url($sec_v['auth_c'] .'/'. $sec_v['auth_a']); ?>"><?php echo $sec_v['auth_name']; ?></a></li>
            <?php endif; endforeach; ?>
        </ul>
        <?php endforeach; ?>
    </div>
    <!-- 右 -->
     <script type="text/javascript" charset="utf-8" src="/plugins/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/plugins/ueditor/ueditor.all.min.js"> </script>
    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
    <script type="text/javascript" charset="utf-8" src="/plugins/ueditor/lang/zh-cn/zh-cn.js"></script>
    <!-- 右 -->
    <div class="content">
        <div class="header">
            <h1 class="page-title">商品编辑</h1>
        </div>
        
        <!-- edit form -->
        <form action="<?php echo url('update'); ?>" method="post" id="tab" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $goods['id']; ?>" >
            <ul class="nav nav-tabs">
              <li role="presentation" class="active"><a href="#basic" data-toggle="tab">基本信息</a></li>
              <li role="presentation"><a href="#desc" data-toggle="tab">商品描述</a></li>
              <li role="presentation"><a href="#attr" data-toggle="tab">商品属性</a></li>
              <li role="presentation"><a href="#pics" data-toggle="tab">商品相册</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="basic">
                    <div class="well">
                        <label>商品名称：</label>
                        <input type="text" name="goods_name" value="<?php echo $goods['goods_name']; ?>" class="input-xlarge">
                        <label>商品价格：</label>
                        <input type="text" name="goods_price" value="<?php echo $goods['goods_price']; ?>" class="input-xlarge">
                        <label>商品数量：</label>
                        <input type="text" name="goods_number" value="<?php echo $goods['goods_number']; ?>" class="input-xlarge">
                        <label>商品logo：</label>
                        <input type="file" name="goods_logo" value="<?php echo $goods['goods_logo']; ?>" class="input-xlarge">
                        <label>商品分类：</label>
                        <select name="" id="cate_one" class="input-xlarge">
                            <option value="">请选择一级分类</option>
                            <?php foreach($cate_one_all as $v): ?>
                            <option value="<?php echo $v['id']; ?>" <?php if($v['id'] == $cate_two['pid']): ?>selected<?php endif; ?>><?php echo $v['cate_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="" id="cate_two" class="input-xlarge">
                            <option value="">请选择二级分类</option>
                            <?php foreach($cate_two_all as $v): ?>
                            <option value="<?php echo $v['id']; ?>" <?php if($v['id'] == $cate_two['id']): ?>selected<?php endif; ?>><?php echo $v['cate_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="cate_id" id="cate_three" class="input-xlarge">
                            <option value="">请选择三级分类</option>
                            <?php foreach($cate_three_all as $v): ?>
                            <option value="<?php echo $v['id']; ?>" <?php if($v['id'] ==$goods['cate_id']): ?>selected<?php endif; ?>><?php echo $v['cate_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="tab-pane fade in" id="desc">
                    <div class="well">
                        <label>商品简介：</label>
                        <textarea id="editor" name="goods_introduce" style="height:800px;width:600px"><?php echo $goods['goods_introduce']; ?></textarea>
                    </div>
                </div>
                <div class="tab-pane fade in" id="attr">
                    <div class="well">
                        <label>商品类型：</label>
                        <select name="type_id"  class="input-xlarge">
                          <option value="">==请选择==</option>
                            <?php foreach($type as $v): ?>
                            <option value="<?php echo $v['id']; ?>" <?php if($v['id'] == $goods['type_id']): ?>selected<?php endif; ?>><?php echo $v['type_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                     <div id="attrs">
                       
                    </div>
                </div>
                <div class="tab-pane fade in" id="pics">
                    <div class="well">
                      <?php foreach($goodspics as $v): ?>
                       <div>
                       
                        <div><image src="<?php echo $v['pics_big']; ?>">[<a href="javascript:void(0);" class="sub">-</a>]<input type="file" id="<?php echo $v['id']; ?>" name="goods_pics[]" value="<?php echo $v['pics_big']; ?>" class="input-xlarge"> </div>
                      </div>           
                      <?php endforeach; ?>
                        <div>[<a href="javascript:void(0);" class="add">+</a>] 商品图片：<input type="file" name="goods_pics[]" value="" class="input-xlarge"></div>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">保存</button>
            </div>
        </form>
        <!-- footer -->
        <footer>
            <hr>
            <p>© 2017 <a href="javascript:void(0);" target="_blank">ADMIN</a></p>
        </footer>
    </div>
    <script type="text/javascript">
        $(function(){
         //启动ue
        var ue = UE.getEditor('editor');
        //给类型绑定change事件,并发送ajax请求
           $('#type_name').change(function(){
             //接收数据,并判定数据
             var id = $(this).val();
             if (id == '') return;
             //发送ajax请求
             $.ajax({
              'url':"<?php echo url('/admin/type/update'); ?>",
              'type':'post',
              'data':'id='+id,
              'dataType':'json',
              'success':function(res){
                   if (res.code !=10000){
                    alert('参数错误');
                   } 
                   //将新的数据展示到页面上
                   // console.log(res.data);
                   $('#type_name').html( '"<option value="'+res.data.id+'">'+res.data.type_name+'</option>"')
              }
             })
           }) 
        //三级联动
        /*
        1 选中目标dom并绑定change事件
        1.1 接收数据 
        2 发送ajax请求获取数据
        3 将获取的数据展示到页面上
         */
        $('#cate_one').change(function(){
             //当点击一级返回权限时,将二级权限清空
              $('#cate_two').html("<option value=''>请选择二级分类</option>");
              $('#cate_three').html("<option value=''>请选择三级分类</option>");
             //接收数据
             var id = $(this).val();
             // console.log(id);return;
             //数据判定
             if (id == '') return;
            //根据pid为0查询数据
            $.ajax({
                'url':"<?php echo url('admin/category/getsubcate'); ?>",
                'type':'post',
                'data':'id=' + id,
                'dataType':'json',
                'success':function(res){
                   //数据判定
                   if(res.code !=10000){
                    alert('请求失败');
                   }
                   //将数据展示到页面上
                   var html = "<option value=''>请选择二级分类</option>";
                   //循环遍历
                  for (var i = 0; i < res.data.length; i++) {
                      //拼接option
                      html += '"<option value="'+res.data[i].id+'">'+res.data[i].cate_name+'</option>"' 
                  }
                  // console.log(html);
                  // 将二级分类的信息添加到页面上
                  $('#cate_two').html(html);
                }
            })
        })

         /*
        1 选中目标dom并绑定change事件
        1.1 接收数据 
        2 发送ajax请求获取数据
        3 将获取的数据展示到页面上
         */
        $('#cate_two').change(function(){
             //当点击一级返回权限时,将二级权限清空
              $('#cate_three').html("<option value=''>请选择三级分类</option>");
             //接收数据
             var id = $(this).val();
             // console.log(id);return;
             //数据判定
             if (id == '') return;
            //根据pid为0查询数据
            $.ajax({
                'url':"<?php echo url('admin/category/getsubcate'); ?>",
                'type':'post',
                'data':'id=' + id,
                'dataType':'json',
                'success':function(res){
                   //数据判定
                   if(res.code !=10000){
                    alert('请求失败');
                   }
                   //将数据展示到页面上
                   var html = "<option value=''>请选择三级级分类</option>";
                   //循环遍历
                  for (var i = 0; i < res.data.length; i++) {
                      //拼接option
                      html += '"<option value="'+res.data[i].id+'">'+res.data[i].cate_name+'</option>"' 
                  }
                  // console.log(html);
                  // 将二级分类的信息添加到页面上
                  $('#cate_three').html(html);
                }
            })
        })
            $('.add').click(function(){
                var add_div = '<div>[<a href="javascript:void(0);" class="sub">-</a>]商品图片：<input type="file" name="goods_pics[]" value="" class="input-xlarge"></div>';
                $(this).parent().after(add_div);
            });
            $('.sub').live('click',function(){
                $(this).parent().remove();
            });


        //给下拉列表绑定change事件
        $('select[name=type_id]').change(function () {
            //获取商品类型id
            // console.log(1);
            var type_id = $(this).val();
            // console.log(type_id);return;
            if (type_id == '') {
                return;
            }
            var data = { 'type_id': type_id };
            //发送ajax请求,获取改类型下的属性名称信息
            $.ajax({
                "url": "<?php echo url('admin/attribute/getattr'); ?>",
                "type": "post",
                "data": data,
                "dataType": "json",
                "success": function (res) {
                    if (res.code != 10000) {
                        alert(res.msg); return;
                    }
                    //处理获取到的数据
                    //属性信息数组
                    var attrs = res.data;
                    //console.log(attrs);
                    //遍历数组,拼接label标签以及input标签
                    var str = '';
                    $.each(attrs, function (i, v) {
                        //v 就是一条属性数据是一个json格式对象
                        str += "<label>" + v.attr_name + ":</label>";
                        if (v.attr_input_type == 0) {
                            //input 输入框
                            str += '<input type="text" name="attr_values['+v.id+'][]" value="" class="input-xlarge">';
                        } else if (v.attr_input_type == 1) {
                            //下拉框
                            str += '<select name="attr_values['+v.id+'][]">';
                            //遍历可选值数组,拼接option
                            $.each(v.attr_values, function (index, value) {
                                str += '<option value="' + value + '">' + value + '</option>';
                            });
                            str += '</select>';
                        } else {
                            //多选框
                            //遍历可选值数组,拼接input checkbox标签
                            $.each(v.attr_values, function (index, value) {
                                str += '<input type="checkbox" name="attr_values['+v.id+'][]" value="' + value + '" >' + value;
                            });
                        }
                    });
                    //console.log(str);return;
                    //将拼接好的字符串放到页面上 对应的div中
                    $('#attrs').html(str);
                }
            });
        });
        });
    </script>

</body>

</html>