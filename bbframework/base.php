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


//  版本信息
define('BB_VERSION', '0.5.5');

// PHP文件后缀
defined('EXT') or define('EXT', '.php');
// 路径分隔符
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// BB框架路径
defined('BB_PATH') or define('BB_PATH', dirname(__FILE__) . DS);
// BB框架LIB路径
defined('BB_LIB_PATH') or define('BB_LIB_PATH', BB_PATH . 'library' . DS);
// BB框架核心包路径
defined('BB_CORE_PATH') or define('BB_CORE_PATH', BB_LIB_PATH . 'bb' . DS);
// BB框架工具包路径
defined('BB_UTIL_PATH') or define('BB_UTIL_PATH', BB_LIB_PATH . 'utils' . DS);
// BB框架第三方库路径
defined('BB_THIRD_PATH') or define('BB_THIRD_PATH', BB_LIB_PATH . 'third' . DS);
// BB框架Trait路径
defined('BB_TRAIT_PATH') or define('BB_TRAIT_PATH', BB_LIB_PATH . 'traits' . DS);

// 设置runtime位置
defined('RUNTIME_PATH') or define('RUNTIME_PATH', realpath(__DIR__) . DS . '..' . DS . 'runtime' . DS);
// 关闭应用自动执行
defined('APP_AUTO_RUN') or define('APP_AUTO_RUN', false);
// 开启HOOK
defined('APP_HOOK') or define('APP_HOOK', true);
// vendor路径
defined('VENDOR_PATH') or define('VENDOR_PATH', BB_PATH . 'vendor' . DS);



// Kafka
defined('KAFKA_CONFIG') or define('KAFKA_CONFIG', 'kafka');
// Kafka默认值
defined('KAFKA_DEFAULT') or define('KAFKA_DEFAULT', 'default');

// 数据库配置项
defined('DB_CONFIG') or define('DB_CONFIG', 'database');
// 默认数据库值
defined('DB_DEFAULT') or define('DB_DEFAULT', 'default');

// ES配置项
defined('ES_CONFIG') or define('ES_CONFIG', 'es');
// 默认数据库值
defined('ES_DEFAULT') or define('ES_DEFAULT', 'default');

// 默认时间格式
defined('TIME_DEFAULT_FORMAT') or define('TIME_DEFAULT_FORMAT', 'Y-m-d H:i:s');
// ES层
defined('EODEL_LAYER') or define('EODEL_LAYER', 'eodel');


// static路径
defined('STATIC_PATH') or define('STATIC_PATH', DS . 'static' . DS);