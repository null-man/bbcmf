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

namespace bb\form;


class BaseForm
{

    // + ---------------------------------------------
    // | 辅助函数 - 抛出异常
    // + ---------------------------------------------
    protected function __empty_exception($method, $param){
        throw new \Exception("[method] $method: $param is empty!");
    }
}