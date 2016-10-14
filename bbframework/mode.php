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

/**
 * BB模式定义
 */
return [
    // 命名空间
    'namespace' => [
        'bb'       => BB_LIB_PATH . 'bb' . DS,
        'util'     => BB_LIB_PATH . 'utils' . DS,
        'bt'       => BB_LIB_PATH . 'traits'. DS,
        ''         => BB_LIB_PATH . 'third'. DS,
    ],

    // 配置文件
    'config'    => BB_PATH . 'config' . EXT,

    // 别名定义
    'alias'     => [
        // 'bb\DB'                       => BB_CORE_PATH . 'DB' . EXT,
        // 'bb\Test'                  => BB_CORE_PATH . 'Test' . EXT,
        // 'util\Tool'                => BB_UTIL_PATH . 'Tool' . EXT,
        // 'util\StringUtils'         => BB_UTIL_PATH . 'StringUtils' . EXT,
        // 'util\IOUtils'             => BB_UTIL_PATH . 'IOUtils' . EXT,
        // 'util\DateUtils'           => BB_UTIL_PATH . 'DateUtils' . EXT,
        // 'util\NetworkUtils'        => BB_UTIL_PATH . 'NetworkUtils' . EXT,
        // 'util\UrlUtils'            => BB_UTIL_PATH . 'UrlUtils' . EXT,
    ],
];