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

return [

    // 是否自动转换URL中的控制器名
    'url_controller_convert' => false,
	// 扩展配置文件
    'extra_config_list'      => ['database', 'route', 'validate', 'es', 'kafka', 'cache', 'admin'],
	// 扩展函数文件
    'extra_file_list'        => [BB_PATH . 'helper' . EXT],


    // +----------------------------------------------------------------------
    // | ES设置
    // +----------------------------------------------------------------------

   	'es'               => [
        // 服务器地址
        'hosts'    => ['localhost:9200'],
        'prefix'   => '',
        'settings' => []
    ], 


    // +----------------------------------------------------------------------
    // | kafka设置
    // +----------------------------------------------------------------------

    'kafka'            => [
        // 服务器地址
        'hosts'    => ['localhost:9020'],
        'groupid' => '',
        'auto.commit.interval.ms' => 1000,
        'offset.store.sync.interval.ms' => 6000,
        'default_topic' => '',
        'partition'     => 1,
    ],


    // +----------------------------------------------------------------------
    // | Redis缓存设置
    // +----------------------------------------------------------------------

    // 'cache'                  => [
    //     // 驱动方式
    //     'type'       => 'Redis',
    //     'host'       => '127.0.0.1',
    //     'port'       => 6379,
    //     'password'   => '',
    //     'timeout'    => false,
    //     'expire'     => false,
    //     'persistent' => false,
    //     'length'     => 0,
    //     'prefix'     => '',
    // ],

    // +----------------------------------------------------------------------
    // | admin后台设置
    // +----------------------------------------------------------------------

    'admin'                 => [
        'title'                 => '宝宝巴士后台',
        'login_service'         => '\\bb\\service\\LoginService',   // 登录Sevice
        'admin_service'         => '\\bb\\service\\AdminService',   // 
        'admin_tmpl_service'    => '\\bb\\service\\AdminTmplService',
        'static'                => 'admin',                         // 目录
        'url'                   => '/admin',
        'encry'                 => true,                    // 密码是否加密
        'post_name'             => 'name',                  // 用户名POST参数名
        'post_password'         => 'password',              // 密码POST参数名
        'login_url'             => '/admin/login',          // 登录页面url
        'login_session_name'    => 'ADMIN_ID',              // 登录sessionID
    ],

];