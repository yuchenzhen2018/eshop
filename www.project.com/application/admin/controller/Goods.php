<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Goods extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        /*1 如果携带参数 则进行查询操作
          2 如果没带参数 则进行展示操作*/
        $query = request()->param('goods_name');
        // $page = request()->param('page');
        // dump($page);die;
        if ($query == '') {
            //展示数据
            $data = \app\admin\model\Goods::order('id desc')->paginate(3);
        }else{
            $data = \app\admin\model\Goods::where('goods_name','like',"%$query%")->paginate(2);
        }
        
        //展示数据到页面上
        return view('index',['data'=>$data]);
    }
     public function look()
    {
        /*1 如果携带参数 则进行查询操作
          2 如果没带参数 则进行展示操作*/
        $query = request()->param('goods_name');
        $data = \app\admin\model\Goods::where('goods_name','like',"%$query%")->paginate(2);
        //展示数据到页面上
        return view('index',['data'=>$data]);
    }
    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //查询所有商品的类型,放到下拉框内
        $type = \app\admin\model\Type::select();
        $cate_one = \app\admin\model\Category::where('pid',0)->select();
        return view('create',['type'=>$type,'cate_one'=>$cate_one]);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //接收数据
        $data = $request->param();
        //数据检测
        //制定规则
        $rule = [
            "goods_name"=>'require',
            "goods_price"=>'require|float|gt:0',
            "goods_number"=>'require|integer|gt:0'
        ];
        //定义提示信息
        $msg = [
          'goods_name.require'=>'名称不能为空',
          'goods_price.require'=>'商品价格不能为空',
          'goods_price.float'=>'商品价格必须为浮点型',
          'goods_price.gt'=>'商品价格必须大于零',
          'goods_number.require'=>'商品数量不能为空',
          'goods_number.integer'=>'商品数量必须为整数',
          'goods_number.gt'=>'商品数量必须大于零'
        ];
        //信息校对
        $validate = new \think\Validate($rule,$msg);
        if (!$validate->check($data)) {
            $error = $validate->getError();
            $this->error($error);
        }
        //图片上传
        $data['goods_logo'] = $this->upload_logo();
        //将数据添加到数据库中
        $goods = \app\admin\model\Goods::create($data,true);
        //上传商品相册
        $this->upload_pics($goods->id);
        // dump($goods->id);die;
        $attr_values = $data['attr_values'];
        //定义空数组
        $goods_attr_data = [];
        foreach ($attr_values as $k=>$v) {
            //循环每一条数据
            //定义一个空数组,放数组属性
            foreach ($v as $value) {
                //循环出每一个属性,组成一条数据
                $row = [
                     'goods_id'=>$goods->id,
                     'attr_id'=>$k,
                    'attr_value'=>$value     
                ]; 
                $goods_attr_data[] = $row; 
            }
        }
        //实例化对象,并添加数据
        $goods_attr = new \app\admin\model\GoodsAttr();
        $goods_attr->saveAll($goods_attr_data);
        //添加成功跳转
        $this->success('数据添加成功','index');
    }


    /*图片上传
    1 修改前端表单
    2 接收图片信息
    3 把图片移动到指定文件夹
    4 如果移动成功,做成缩略图并把文件名返回*/
    private function upload_logo(){
      //接受图片信息
        $file = request()->file('logo');
        // dump($file);die;
        //判定图片不能为为空
        if (empty($file)) {
            $this->error('商品logo必须上传');
        }
        //将图片移动到pulic/upload/
        $info = $file -> validate(['size'=>5*1024*1024,'ext'=>['gif','jpg','jepg','png']])->move(ROOT_PATH .'public'.DS.'uploads');
        //判定
        if ($info) {
            //如果上传成功返回完整路径
            $goods_logo = DS . 'uploads' . DS . $info->getSaveName();
            //做成缩略图
            $image = \think\Image::open('.'.$goods_logo);
            //原图已经没用了,用缩略图,覆盖原图
            $image -> thumb(50,50)->save('.'.$goods_logo);
            return $goods_logo;
        }else{
            //上传失败,返回并告知错误
            $error = $file->getError();
            $this->error($error);
        }
    }

    //多张图片上传
    /*
    1 接收图片信息
    2 循环每一张图片的信息,用foreach
    3 把图片放到指定文件夹下
    4 如果放成功了,拼接图片信息,拼接成有事件类型的图片
    5 拼接两个大小的缩略图,并把缩略图放到指定文件夹下
    6 将商品goods_id和两个缩略图放进一个数组中,然后放进一个数组中组成二位数组
    7 调用模板,放入数据库中
    */ 
    private function upload_pics($goods_id){
      //接收图片信息
      $files = request()->file('goods_pics');
      //数据判定
      if (empty($files)) {
          $this->error('商品相册不能为空');
      }
      //循环得到每一张图片信息,实现单文件上传
       foreach ($files as $file) { 
            $data = [];
            //将图片放到指定文件夹下
            $info = $file->Validate(['size'=>6*1024*1024,'ext'=>'jpg','gif','png'])->move(ROOT_PATH .'public'.DS.'uploads');
            //结果判定
            if ($info) {
                //拼接图片访问路径
                $pics_origin = DS . 'uploads' .DS. $info->getSaveName();
                // dump($pics_origin);die;
                //生成两张800*800和400*400的缩略图
                //先生成两个时间和字符串20180909/thumb_800_djafakfajf.jpg
                $temp = explode(DS,$info->getSaveName());
                //拼接缩略图的访问路径
                $pics_big = DS.'uploads'.DS.$temp[0].DS.'thumb_800_'.$temp[1];
                $pics_sma = DS.'uploads'.DS.$temp[0].DS.'thumb_400_'.$temp[1];
                //做成缩略图
                // 打开一张图片
                $image = \think\Image::open('.'.$pics_origin);
                $image->thumb(800,800)->save('.'.$pics_big);
                $image->thumb(400,400)->save('.'.$pics_sma);
                //将图片放到一个数组中
                $row = [
                    'goods_id'=>$goods_id,
                    'pics_big'=>$pics_big,
                    'pics_sma'=>$pics_sma,
                ];
                //然后循环结束后,把所有的数组保存到一个数组中
                $goodspics_data[] = $row;
            }
       }
       $goodspics = new \app\admin\model\Goodspics();
       $goodspics->saveAll($goodspics_data,true);
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
        /*根据id查询数据
          将数据展示到页面上*/
          $goods = \app\admin\model\Goods::find($id);
          //获取一级分类
          $cate_one_all = \app\admin\model\Category::where('pid',0)->select();
          //根据商品id查找三级分类
          $cate_three = \app\admin\model\Category::find($goods['cate_id']);
          //根据三级分类查找二级分类
          $cate_two = \app\admin\model\Category::find($cate_three['pid']);
          // dump($cate_two);die;
         //获取所有的二级分类
         $cate_two_all = \app\admin\model\Category::where('pid',$cate_two['pid'])->select();
         //获取所有的三级分类
         $cate_three_all = \app\admin\model\Category::where('pid',$cate_three['pid'])->select();
         //查询你商品的类型
         $type = \app\admin\model\Type::select();
         // dump($type);die;
         $goodspics = \app\admin\model\Goodspics::where('goods_id',$id)->select();
         // dump($goodspics);die;
          return view('edit',[
             'goods'=>$goods,
             'cate_one_all'=>$cate_one_all,
             'cate_two_all'=>$cate_two_all,
             'cate_three_all'=>$cate_three_all,
             'cate_two'=>$cate_two,
             'type'=>$type,
             'goodspics'=>$goodspics
          ]);

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
        //接收数据做更新操作
        $data = $request->param();
        // dump($data);die;
        //数据检测 略
        \app\admin\model\Goods::update($data,['id'=>$id],true);
        //页面跳转
        $this->redirect('index');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //对$id进行检测
        /*id
         * id必须时证数
         * id必须时整数
         */
        if(!preg_match('/^\d+$/', $id) || $id==0){
            $this->error('参数错误');
        }
        \app\admin\model\Goods::destroy($id);
        //页面跳转
        $this->redirect('admin/goods/index');
    }
}
