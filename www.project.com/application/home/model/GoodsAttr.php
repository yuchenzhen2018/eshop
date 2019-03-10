<?php

namespace app\home\model;

use think\Model;

class GoodsAttr extends Model
{
   public static function getAttrByIds($goods_attr_ids){
    //查询属性值表tpshop_goods_attr 链表 tpshop_attribute
       return self::alias('t1')
           ->field('t1.*,t2.attr_name')
           ->join('tpshop_attribute t2','t1.attr_id=t2.id','left')
           ->where('t1.id', 'in', $goods_attr_ids)
           ->select();
   }
}
