<?php
// +----------------------------------------------------------------------
// | BBFramework
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2016 http://www.babybus.com/ All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ZergL <lin029011@163.com>
// +----------------------------------------------------------------------

// namespace bb;

function config_select($config) {

    if(!isset($config['default'])) {
        return $config;
    }

    if(empty($_GET['_config_'])) {
        return $config['default'];
    }
    return $config[$_GET['_config_']];
}


// 加载BB模式定义文件
$mode = require __DIR__ . DS . 'mode' . EXT;
\think\Loader::addNamespace($mode['namespace']);
\think\Loader::addMap($mode['alias']);
\think\Config::load($mode['config']);


