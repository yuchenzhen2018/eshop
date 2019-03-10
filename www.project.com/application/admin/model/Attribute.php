<?php

namespace app\admin\model;

use think\Model;

class Attribute extends Model
{
    public function getAttrTypeAttr($value){
        //0:唯一属性 1代表当选属性
        return $value ? "单选属性" : "唯一属性";
    }
    public function getAttrInputTypeAttr($value){
        //0:input输入框 1下拉列表 2 代表多选框
        $attr_input_type = ['input输入框','下拉列表','多选框'];
        return $attr_input_type[$value];
    }
}
