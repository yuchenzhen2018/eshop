<?php

namespace app\admin\model;

use think\Model;

class Auth extends Model
{
    public function getIsNavAttr($value){
       //is_nav字段 1是 0否
        return $value ? '是' : '否';
    }
}
