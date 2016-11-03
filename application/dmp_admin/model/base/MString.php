<?php
// +----------------------------------------------------------------------
// | BBFramework
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2016 http://www.babybus.com/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NullYang <635384073@qq.com>
// +----------------------------------------------------------------------

namespace app\dmp_admin\model\base;

class MString extends MBase
{
    public static $type = 'string';

    function __construct(){
        parent::__construct();
    }



    // + ---------------------------------------------
    // | 表单 - 添加
    // + ---------------------------------------------
    public static function form_input($body, $value = ''){
        self::$instance->body_text($body, $value);
    }




    // + ---------------------------------------------
    // | 表单 - 模块添加
    // + ---------------------------------------------
    public static function form_block_input($name, $body, $value = ''){
        self::$instance->body_block_text($name, $body, $value);
    }
}