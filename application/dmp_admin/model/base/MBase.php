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
use bb\Model;
use util\TmplUtils;

class MBase extends Model
{
    // Tmpl 单例
    protected static $instance = null;


    // 构造函数 初始化tmpl类
    // + ---------------------------------------------
    // | 构造函数 - 初始化 tmpl 类
    // + ---------------------------------------------
    function __construct()
    {
        parent::__construct();

        if(is_null(self::$instance)){
            self::$instance = new TmplUtils();
            self::$instance = self::$instance->_init();
        }
    }
}