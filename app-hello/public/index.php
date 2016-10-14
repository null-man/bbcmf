<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

// 定义后台目录
define('ADMIN_PATH', __DIR__ . '/../application/admin/');

// 开启调试模式
define('APP_DEBUG', true);

// 关闭多模块设计
// define('APP_MULTI_MODULE',false);

// define('TMPL_PATH', './tmpl/');

require __DIR__ . '/../../bbframework/base.php';

// 加载框架引导文件
require __DIR__ . '/../../thinkphp/start.php';


require __DIR__ . '/../../bbframework/start.php';


// 路由绑定index模块
// \think\Route::bind('module','dmp_admin');
// \think\Route::bind('module','index');
// \think\Route::bind('module','index/index');



\think\App::run();