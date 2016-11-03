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

$app = '';
if(isset($_GET['_app_'])) {
	$app = $_GET['_app_'];
}
$multi = true;
if(isset($_GET['_multi_'])) {
	$multi = (bool) intval($_GET['_multi_']);
}
$debug = true;
if(isset($_GET['_debug_'])) {
	$debug = false;
}


if(empty($app)) {
	// 定义应用目录
	define('APP_PATH', __DIR__ . '/../application/');

	// 定义后台目录
	define('ADMIN_PATH', __DIR__ . '/../application/admin/');
} else {
	// 定义应用目录
	define('APP_PATH', __DIR__ . '/../app-'.$app.'/application/');
	// 定义后台目录
	define('ADMIN_PATH', __DIR__ . '/../app-'.$app.'/application/admin/');
}


// 关闭多模块设计
define('APP_MULTI_MODULE', $multi);

// cookie
define('SITE_URL', 'http://'.$_SERVER['SERVER_NAME'].'/');

// 开启调试模式
define('APP_DEBUG', $debug);


// define('TMPL_PATH', './tmpl/');

require __DIR__ . '/../bbframework/base.php';

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';


require __DIR__ . '/../bbframework/start.php';

// 路由绑定index模块
// \think\Route::bind('module','dmp_admin');
// \think\Route::bind('module','index');
// \think\Route::bind('module','index/index');

// 安装向导
if(!file_exists("data/install.lock")){
	if(!isset($_GET['g']) || strtolower($_GET['g']) != "install"){
		header("Location:/index.php/install?_app_=cmf&g=install");
		exit();
	}
}


\think\App::run();